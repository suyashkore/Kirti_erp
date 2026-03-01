<?php



use app\services\utilities\Arr;



defined('BASEPATH') or exit('No direct script access allowed');



class Clients_model extends App_Model
{

    private $contact_columns;



    public function __construct()
    {

        parent::__construct();



        $this->contact_columns = hooks()->apply_filters('contact_columns', ['firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'ticket_emails', 'is_primary']);



        $this->load->model(['client_vault_entries_model', 'client_groups_model', 'statement_model']);

    }



    /**

     * Get client object based on passed clientid if not passed clientid return array of all clients

     * @param  mixed $id    client id

     * @param  array  $where

     * @return mixed

     */

    public function get($id = '', $where = [])
    {

        $this->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'clients')) . ',' . get_sql_select_client_company());



        $this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'clients.country', 'left');

        $this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.userid = ' . db_prefix() . 'clients.userid AND is_primary = 1', 'left');



        if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {

            $this->db->where($where);

        }



        if (is_numeric($id)) {

            $this->db->where(db_prefix() . 'clients.userid', $id);

            $client = $this->db->get(db_prefix() . 'clients')->row();



            if ($client && get_option('company_requires_vat_number_field') == 0) {

                $client->vat = null;

            }



            $GLOBALS['client'] = $client;



            return $client;

        }



        $this->db->order_by('company', 'asc');



        return $this->db->get(db_prefix() . 'clients')->result_array();

    }



    /**

     * Get customers contacts

     * @param  mixed $customer_id

     * @param  array $where       perform where query

     * @param  array $whereIn     perform whereIn query

     * @return array

     */

    public function get_contacts($customer_id = '', $where = ['active' => 1], $whereIn = [])
    {

        $this->db->where($where);

        if ($customer_id != '') {

            $this->db->where('userid', $customer_id);

        }



        foreach ($whereIn as $key => $values) {

            if (is_string($key) && is_array($values)) {

                $this->db->where_in($key, $values);

            }

        }



        $this->db->order_by('is_primary', 'DESC');



        return $this->db->get(db_prefix() . 'contacts')->result_array();

    }



    /**

     * Get single contacts

     * @param  mixed $id contact id

     * @return object

     */

    public function get_contact($id)
    {

        $this->db->where('id', $id);



        return $this->db->get(db_prefix() . 'contacts')->row();

    }



    /**

     * Get contact by given email

     *

     * @since 2.8.0

     *

     * @param  string $email

     *

     * @return \strClass|null

     */

    public function get_contact_by_email($email)
    {

        $this->db->where('email', $email);

        $this->db->limit(1);



        return $this->db->get('contacts')->row();

    }



    /**

     * @param array $_POST data

     * @param withContact

     *

     * @return integer Insert ID

     *

     * Add new client to database

     */

    public function add($data, $withContact = false)
    {

        $contact_data = [];

        // From Lead Convert to client

        if (isset($data['send_set_password_email'])) {

            $contact_data['send_set_password_email'] = true;

        }



        if (isset($data['donotsendwelcomeemail'])) {

            $contact_data['donotsendwelcomeemail'] = true;

        }



        $data = $this->check_zero_columns($data);



        $data = hooks()->apply_filters('before_client_added', $data);



        foreach ($this->contact_columns as $field) {

            if (!isset($data[$field])) {

                continue;

            }



            $contact_data[$field] = $data[$field];



            // Phonenumber is also used for the company profile

            if ($field != 'phonenumber') {

                unset($data[$field]);

            }

        }



        $groups_in = Arr::pull($data, 'groups_in') ?? [];

        $custom_fields = Arr::pull($data, 'custom_fields') ?? [];



        // From customer profile register

        if (isset($data['contact_phonenumber'])) {

            $contact_data['phonenumber'] = $data['contact_phonenumber'];

            unset($data['contact_phonenumber']);

        }



        $this->db->insert(db_prefix() . 'clients', array_merge($data, [

            'datecreated' => date('Y-m-d H:i:s'),

            'addedfrom' => is_staff_logged_in() ? get_staff_user_id() : 0,

        ]));



        $client_id = $this->db->insert_id();



        if ($client_id) {

            if (count($custom_fields) > 0) {

                $_custom_fields = $custom_fields;

                // Possible request from the register area with 2 types of custom fields for contact and for comapny/customer

                if (count($custom_fields) == 2) {

                    unset($custom_fields);

                    $custom_fields['customers'] = $_custom_fields['customers'];

                    $contact_data['custom_fields']['contacts'] = $_custom_fields['contacts'];

                } elseif (count($custom_fields) == 1) {

                    if (isset($_custom_fields['contacts'])) {

                        $contact_data['custom_fields']['contacts'] = $_custom_fields['contacts'];

                        unset($custom_fields);

                    }

                }



                handle_custom_fields_post($client_id, $custom_fields);

            }



            /**

             * Used in Import, Lead Convert, Register

             */

            if ($withContact == true) {

                $contact_id = $this->add_contact($contact_data, $client_id, $withContact);

            }



            foreach ($groups_in as $group) {

                $this->db->insert('customer_groups', [

                    'customer_id' => $client_id,

                    'groupid' => $group,

                ]);

            }



            $log = 'ID: ' . $client_id;



            if ($log == '' && isset($contact_id)) {

                $log = get_contact_full_name($contact_id);

            }



            $isStaff = null;



            if (!is_client_logged_in() && is_staff_logged_in()) {

                $log .= ', From Staff: ' . get_staff_user_id();

                $isStaff = get_staff_user_id();

            }



            do_action_deprecated('after_client_added', [$client_id], '2.9.4', 'after_client_created');



            hooks()->do_action('after_client_created', [

                'id' => $client_id,

                'data' => $data,

                'contact_data' => $contact_data,

                'custom_fields' => $custom_fields,

                'groups_in' => $groups_in,

                'with_contact' => $withContact,

            ]);



            log_activity('New Client Created [' . $log . ']', $isStaff);

        }



        return $client_id;

    }



    /**

     * Insert data into tblclients table

     * Maps form fields to database columns

     * @param  array $form_data Form POST data

     * @param  integer $userid User ID

     * @param  integer $plantid Plant ID

     * @return mixed Client ID or false

     */

    public function add_to_tblclients($form_data, $userid= 0 )
    {

        // $this->db->truncate('tblclients');
        // $this->db->truncate('tblBankMaster');
        // $this->db->truncate('tblcontacts');
        // $this->db->truncate('tblclientwiseshippingdata');
        // die;

        // echo"<pre>";
        // print($form_data);
        // die;

        // prefer PlantID and userid from form data if provided
         $plant = $this->session->userdata('root_company');
        $user = isset($form_data['userid']) ? $form_data['userid'] : $userid;

        	// Initialize insert data array
		$insert_data = [];

		// Fetch ActSubGroupID1 and ActGroupID using JOIN query
		if (isset($form_data['groups_in']) && !empty($form_data['groups_in'])) {
			$this->db->select('sg2.SubActGroupID1, sg1.ActGroupID');
			$this->db->from('tblAccountSubGroup2 sg2');
			$this->db->join('tblAccountSubGroup1 sg1', 'sg2.SubActGroupID1 = sg1.SubActGroupID1', 'left');
			$this->db->where('sg2.SubActGroupID', $form_data['groups_in']);
			$group_result = $this->db->get()->row();
			
			if ($group_result) {
				if (isset($group_result->SubActGroupID1)) {
					$insert_data['ActSubGroupID1'] = $group_result->SubActGroupID1;
					log_message('debug', 'SaveVendor: ActSubGroupID1 fetched for groups_in ' . $form_data['groups_in'] . ' = ' . $group_result->SubActGroupID1);
				}
				if (isset($group_result->ActGroupID)) {
					$insert_data['ActGroupID'] = $group_result->ActGroupID;
					log_message('debug', 'SaveVendor: ActGroupID fetched for ActSubGroupID1 ' . $insert_data['ActSubGroupID1'] . ' = ' . $group_result->ActGroupID);
				}
			} else {
				log_message('debug', 'SaveVendor: ActGroupID/ActSubGroupID1 not found for groups_in ' . $form_data['groups_in']);
			}
		}

        
        // mapping of possible incoming fields -> tblclients columns
        $mapping = [
            'AccountID' => isset($form_data['AccountID']) ? $form_data['AccountID'] : null,
            'PlantID' => $plant,
            'userid' => $user,
            'company' => isset($form_data['AccoountName']) ? $form_data['AccoountName'] : null,
            'FavouringName' => isset($form_data['favouring_name']) ? $form_data['favouring_name'] : null,
            'PAN' => isset($form_data['Pan']) ? $form_data['Pan'] : null,
            'GSTIN' => isset($form_data['vat']) ? $form_data['vat'] : null,
            'billing_country' => isset($form_data['country']) ? $form_data['country'] : null,
            'billing_state' => isset($form_data['state']) ? $form_data['state'] : null,
            'billing_city' => isset($form_data['city']) ? $form_data['city'] : null,
            'billing_zip' => isset($form_data['zip']) ? $form_data['zip'] : null,
            'billing_address' => isset($form_data['address']) ? $form_data['address'] : null,
            'MobileNo' => isset($form_data['phonenumber']) ? $form_data['phonenumber'] : null,
            'AltMobileNo' => isset($form_data['altphonenumber']) ? $form_data['altphonenumber'] : null,
            'Email' => isset($form_data['email']) ? $form_data['email'] : null,
            'IsTDS' => isset($form_data['Tds']) ? ($form_data['Tds'] == 1 ? 'Y' : 'N') : null,
            'TDSSection' => isset($form_data['Tdsselection']) ? $form_data['Tdsselection'] : null,
            'TDSPer' => isset($form_data['TdsPercent']) ? $form_data['TdsPercent'] : null,
            'default_currency' => isset($form_data['default_currency']) ? $form_data['default_currency'] : null,

            // Billing Information fields
            'OrganisationType' => isset($form_data['organisation_type']) ? $form_data['organisation_type'] : null,
            'GSTType' => isset($form_data['gsttype']) ? $form_data['gsttype'] : (isset($form_data['gst_type']) ? $form_data['gst_type'] : null),

            // Credit / Payment / Bank Information fields
            'PaymentTerms' => isset($form_data['payment_terms']) ? $form_data['payment_terms'] : null,
            'PaymentCycle' => isset($form_data['payment_cycle']) ? $form_data['payment_cycle'] : null,
            'PaymentCycleType' => isset($form_data['payment_cycle_type']) ? $form_data['payment_cycle_type'] : null,
            'GraceDay' => isset($form_data['credit_days']) ? $form_data['credit_days'] : null,
            'CreditLimit' => isset($form_data['MaxCrdAmt']) ? $form_data['MaxCrdAmt'] : null,
            'FreightTerms' => isset($form_data['freight_term']) ? $form_data['freight_term'] : null,

            // Other Information fields
            'TAN' => isset($form_data['tan_number']) ? $form_data['tan_number'] : null,
            'PriorityID' => isset($form_data['priority']) ? $form_data['priority'] : null,
            'FSSAINo' => isset($form_data['FLNO1']) ? $form_data['FLNO1'] : null,
            'FSSAIExpiry' => isset($form_data['expiry_licence']) ? $form_data['expiry_licence'] : null,
            'TerritoryID' => isset($form_data['territory']) ? $form_data['territory'] : null,
            'website' => isset($form_data['website']) ? $form_data['website'] : null,
            'Attachment' => isset($form_data['attachment']) ? $form_data['attachment'] : (isset($form_data['Attachment']) ? $form_data['Attachment'] : null),
            'AdditionalInfo' => isset($form_data['additional_info']) ? $form_data['additional_info'] : null,

            // Group/Customer Category
             'ActSubGroupID2' => isset($form_data['groups_in']) ? $form_data['groups_in'] : (isset($form_data['ActSubGroupID2']) ? $form_data['ActSubGroupID2'] : null),
			 'ActSubGroupID1' => isset($insert_data['ActSubGroupID1']) ? $insert_data['ActSubGroupID1'] : null,
			 'ActMainGroupID' => isset($insert_data['ActGroupID']) ? $insert_data['ActGroupID'] : null,
             'DistributorType' => isset($form_data['distributor_type']) ? $form_data['distributor_type'] : null,

            // Active/Blocked Status
            'IsActive' => isset($form_data['Blockyn']) ? $form_data['Blockyn'] : null,
            'DeActiveReason' => isset($form_data['blocked_reason']) ? $form_data['blocked_reason'] : null,

            'CreatedBy' => is_staff_logged_in() ? get_staff_user_id() : 0,
            'TransDate' => date('Y-m-d H:i:s'),
            'UserID2' => $user,
            'Lupdate' => date('Y-m-d H:i:s'),
        ];

        // Build final insert data (only include non-null values)
        $insert_data = [];
        foreach ($mapping as $col => $val) {
            if ($val !== null && $val !== '') {
                $insert_data[$col] = $val;
            }
        }

        // Convert date fields from DD/MM/YYYY to YYYY-MM-DD format
        if (isset($insert_data['FSSAIExpiry']) && !empty($insert_data['FSSAIExpiry'])) {
            $original_date = $insert_data['FSSAIExpiry'];
            $converted_date = $this->convert_date_format($insert_data['FSSAIExpiry']);
            log_message('debug', 'FSSAIExpiry conversion: Original=' . $original_date . ', Converted=' . ($converted_date ? $converted_date : 'NULL'));
            if ($converted_date !== null) {
                $insert_data['FSSAIExpiry'] = $converted_date;
            } else {
                // If conversion failed, remove the field to avoid storing invalid date
                unset($insert_data['FSSAIExpiry']);
            }
        }

        // Debug: Log what we're about to insert
        log_message('debug', 'add_to_tblclients insert_data: ' . json_encode($insert_data));
        log_message('debug', 'add_to_tblclients form_data: expiry_licence=' . (isset($form_data['expiry_licence']) ? $form_data['expiry_licence'] : 'NOT_SET'));
        log_message('debug', 'add_to_tblclients form_data keys: ' . implode(',', array_keys($form_data)));

        // AccountID is required for this usage
        if (!isset($insert_data['AccountID']) || empty($insert_data['AccountID'])) {
            return false;
        }

        try {
            $this->db->insert('tblclients', $insert_data);
            if ($this->db->affected_rows() > 0) {
                // If bank details were provided, insert into tblBankMaster as well
                if (isset($form_data['is_bank_detail']) && $form_data['is_bank_detail']) {
                    $this->insert_or_update_tblBankMaster($insert_data['AccountID'], $form_data, $user, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
                }

                // Handle contact data insertion into tblcontacts
                if (isset($form_data['ContactData']) && !empty($form_data['ContactData'])) {
                    $this->insert_contacts_into_tblcontacts($form_data['ContactData'], $insert_data['AccountID'], $plant, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
                }

                // Handle shipping data insertion into tblclientwiseshippingdata
                if (isset($form_data['ShippingData']) && !empty($form_data['ShippingData'])) {
                    $this->insert_shipping_data_into_tblclientwiseshippingdata($form_data['ShippingData'], $insert_data['AccountID'], isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
                }

                // Handle broker master data insertion into tblPartyBrokerMaster
                if (isset($form_data['broker']) && !empty($form_data['broker'])) {
                    $broker_contact_id = (isset($form_data['broker_person']) && !empty($form_data['broker_person'])) ? $form_data['broker_person'] : 0;
                    $this->insert_broker_master_data($insert_data['AccountID'], $form_data['broker'], $broker_contact_id, $plant, $user, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
                }

                // Return the AccountID back to caller (frontend expects this)
                return $insert_data['AccountID'];

            }
        } catch (Exception $e) {
            log_message('error', 'tblclients insert error: ' . $e->getMessage());
            log_message('error', 'tblclients insert_data: ' . json_encode($insert_data));
            return false;
        }

        return false;
    }

    /**
     * Insert or Update contacts into tblcontacts table
     * @param  string $contact_data JSON string containing contact information
     * @param  string $account_id Account ID
     * @param  int $plant_id Plant ID
     * @param  int $user_id User ID
     * @param  boolean $is_update If true, deletes old records first (UPDATE mode), else appends (INSERT mode)
     * @return void
     */
    private function insert_contacts_into_tblcontacts($contact_data, $account_id, $plant_id, $user_id, $is_update = false)
    {
        try {
            // Parse contact data
            $contacts = json_decode($contact_data, true);

            log_message('debug', 'insert_contacts_into_tblcontacts - Parsed contacts: ' . json_encode($contacts) . ' - Update Mode: ' . ($is_update ? 'true' : 'false'));

            if (!is_array($contacts) || empty($contacts)) {
                log_message('debug', 'No contacts to insert for AccountID: ' . $account_id);
                return;
            }

            // Get PlantID from session if not provided
            if (empty($plant_id)) {
                $plant_id = $this->session->userdata('root_company');
                log_message('debug', 'PlantID was empty, using session value: ' . $plant_id);
            }

            // If UPDATE mode, delete existing contacts first
            if ($is_update === true) {
                $this->db->where('AccountID', $account_id);
                $this->db->delete('tblcontacts');
                log_message('debug', 'Deleted existing contacts for AccountID: ' . $account_id);
            }

            $current_date = date('Y-m-d H:i:s');
            $is_primary = 'Y';  // First contact will be primary
            $inserted_count = 0;

            foreach ($contacts as $index => $contact) {
                log_message('debug', 'Processing contact index ' . $index . ': ' . json_encode($contact));

                // Map contact fields to tblcontacts columns
                $contact_insert = [
                    'PlantID' => $plant_id,
                    'TransDate' => $current_date,
                    'AccountID' => $account_id,
                    'firstname' => isset($contact['Name']) && !empty($contact['Name']) ? $contact['Name'] : null,
                    'PositionID' => isset($contact['Designation']) && !empty($contact['Designation']) ? $contact['Designation'] : null,
                    'phonenumber' => isset($contact['Mobile']) && !empty($contact['Mobile']) ? $contact['Mobile'] : null,
                    'email' => isset($contact['Email']) && !empty($contact['Email']) ? $contact['Email'] : null,
                    'IsSmsYN' => isset($contact['SendSMS']) && $contact['SendSMS'] == 1 ? 'Y' : 'N',
                    'IsEmailYN' => isset($contact['SendEmail']) && $contact['SendEmail'] == 1 ? 'Y' : 'N',
                    'is_primary' => $is_primary,
                    'IsActive' => 'Y',
                    'UserID' => $user_id,
                    'UserID2' => $user_id,
                    'Lupdate' => $current_date
                ];

                log_message('debug', 'Contact insert array before filter: ' . json_encode($contact_insert));

                // Remove empty/null values before insert
                $contact_insert = array_filter($contact_insert, function ($v) {
                    return $v !== null && $v !== '';
                });

                log_message('debug', 'Contact insert array after filter: ' . json_encode($contact_insert));

                // Ensure required fields are present
                if (empty($account_id) || empty($contact_insert['AccountID'])) {
                    log_message('debug', 'Skipping contact insertion - missing AccountID for index ' . $index);
                    continue;
                }

                if (empty($contact_insert['firstname'])) {
                    log_message('debug', 'Skipping contact insertion - missing firstname for index ' . $index);
                    continue;
                }

                // Insert contact record
                $this->db->insert('tblcontacts', $contact_insert);

                $affected = $this->db->affected_rows();
                log_message('debug', 'Contact insert affected rows: ' . $affected);

                if ($affected > 0) {
                    log_message('debug', 'Contact inserted successfully for AccountID: ' . $account_id . ' - Name: ' . (isset($contact['Name']) ? $contact['Name'] : 'N/A'));
                    $inserted_count++;
                    $is_primary = 'N';  // Only first contact is primary
                } else {
                    log_message('error', 'Failed to insert contact for AccountID: ' . $account_id . ' - Contact data: ' . json_encode($contact_insert) . ' - Last DB Error: ' . $this->db->error()['message']);
                }
            }

            log_message('debug', 'Total contacts inserted: ' . $inserted_count);
        } catch (Exception $e) {
            log_message('error', 'Error inserting contacts into tblcontacts: ' . $e->getMessage() . ' - Stack: ' . $e->getTraceAsString());
        }
    }

    /**
     * Insert or Update shipping/location data into tblclientwiseshippingdata table
     * @param  string $shipping_data JSON string containing shipping location information
     * @param  string $account_id Account ID
     * @param  int $user_id User ID
     * @param  boolean $is_update If true, deletes old records first (UPDATE mode), else appends (INSERT mode)
     * @return void
     */
    private function insert_shipping_data_into_tblclientwiseshippingdata($shipping_data, $account_id, $user_id, $is_update = false)
    {
        try {
            // Parse shipping data
            $shipping_locations = json_decode($shipping_data, true);

            log_message('debug', 'insert_shipping_data_into_tblclientwiseshippingdata - Parsed locations: ' . json_encode($shipping_locations) . ' - Update Mode: ' . ($is_update ? 'true' : 'false'));

            if (!is_array($shipping_locations) || empty($shipping_locations)) {
                log_message('debug', 'No shipping data to insert for AccountID: ' . $account_id);
                return;
            }

            // If UPDATE mode, delete existing shipping records first
            if ($is_update === true) {
                $this->db->where('AccountID', $account_id);
                $this->db->delete('tblclientwiseshippingdata');
                log_message('debug', 'Deleted existing shipping data for AccountID: ' . $account_id);
            }

            $current_date = date('Y-m-d H:i:s');
            $inserted_count = 0;

            foreach ($shipping_locations as $index => $location) {
                log_message('debug', 'Processing shipping location index ' . $index . ': ' . json_encode($location));

                // Skip empty rows
                if (
                    empty($location['state']) && empty($location['city']) && empty($location['address']) &&
                    empty($location['pincode']) && empty($location['mobile'])
                ) {
                    log_message('debug', 'Skipping empty shipping location at index ' . $index);
                    continue;
                }

                // Map location fields to tblclientwiseshippingdata columns
                $shipping_insert = [
                    'AccountID' => $account_id,
                    'ShippingPin' => isset($location['pincode']) && !empty($location['pincode']) ? $location['pincode'] : null,
                    'ShippingAdrees' => isset($location['address']) && !empty($location['address']) ? $location['address'] : null,
                    'ShippingState' => isset($location['state']) && !empty($location['state']) ? $location['state'] : null,
                    'ShippingCity' => isset($location['city']) && !empty($location['city']) ? $location['city'] : null,
                    'MobileNo' => isset($location['mobile']) && !empty($location['mobile']) ? $location['mobile'] : null,
                    'UserID' => $user_id,
                    'TransDate' => $current_date
                ];

                log_message('debug', 'Shipping insert array before filter: ' . json_encode($shipping_insert));

                // Remove null/empty values before insert
                $shipping_insert = array_filter($shipping_insert, function ($v) {
                    return $v !== null && $v !== '';
                });

                log_message('debug', 'Shipping insert array after filter: ' . json_encode($shipping_insert));

                // Ensure required fields are present (AccountID and at least State and City)
                if (empty($shipping_insert['AccountID'])) {
                    log_message('debug', 'Skipping shipping data insertion - missing AccountID for index ' . $index);
                    continue;
                }

                if (empty($shipping_insert['ShippingState']) || empty($shipping_insert['ShippingCity'])) {
                    log_message('debug', 'Skipping shipping data insertion - missing State or City for index ' . $index);
                    continue;
                }

                // Insert shipping location record
                $this->db->insert('tblclientwiseshippingdata', $shipping_insert);

                $affected = $this->db->affected_rows();
                log_message('debug', 'Shipping insert affected rows: ' . $affected);

                if ($affected > 0) {
                    log_message('debug', 'Shipping location inserted successfully for AccountID: ' . $account_id . ' - City: ' . (isset($location['city']) ? $location['city'] : 'N/A'));
                    $inserted_count++;
                } else {
                    log_message('error', 'Failed to insert shipping location for AccountID: ' . $account_id . ' - Shipping data: ' . json_encode($shipping_insert) . ' - Last DB Error: ' . $this->db->error()['message']);
                }
            }

            log_message('debug', 'Total shipping locations inserted: ' . $inserted_count);
        } catch (Exception $e) {
            log_message('error', 'Error inserting shipping data into tblclientwiseshippingdata: ' . $e->getMessage() . ' - Stack: ' . $e->getTraceAsString());
        }
    }

    /**
     * Insert or Update broker master data into tblPartyBrokerMaster table
     * @param  string $account_id Account ID (Customer/Vendor)
     * @param  string $broker_id Broker ID (Broker AccountID)
     * @param  int $broker_contact_id Broker Contact ID from tblcontacts (optional, can be 0)
     * @param  string $plant_id Plant ID
     * @param  string $user_id User ID
     * @param  string $created_by Created By User ID
     * @param  boolean $is_update If true, deletes old records first (UPDATE mode), else appends (INSERT mode)
     * @return void
     */
    private function insert_broker_master_data($account_id, $broker_id, $broker_contact_id, $plant_id, $user_id, $created_by, $is_update = false)
    {
        try {
            // Validate required fields (only AccountID and BrokerID are required, BrokerContactID is optional)
            if (empty($account_id) || empty($broker_id)) {
                log_message('debug', 'insert_broker_master_data - Missing required fields: AccountID=' . $account_id . ', BrokerID=' . $broker_id);
                return;
            }

            // Get PlantID from session if not provided
            if (empty($plant_id)) {
                $plant_id = $this->session->userdata('root_company');
                log_message('debug', 'PlantID was empty, using session value: ' . $plant_id);
            }

            // If UPDATE mode, delete existing broker records first
            if ($is_update === true) {
                $this->db->where('AccountID', $account_id);
                $this->db->delete('tblPartyBrokerMaster');
                log_message('debug', 'Deleted existing broker data for AccountID: ' . $account_id);
            }

            $current_date = date('Y-m-d H:i:s.u');

            // Build broker master insert data
            $broker_insert = [
                'PlantID' => $plant_id,
                'AccountID' => $account_id,
                'BrokerID' => $broker_id,
                'BrokerContactID' => (int)$broker_contact_id, // Can be 0 if not provided
                'UserID' => $user_id,
                'TransDate' => $current_date,
                'UserID2' => $created_by,
                'Lupdate' => $current_date
            ];

            log_message('debug', 'Broker master insert data: ' . json_encode($broker_insert) . ' - Update Mode: ' . ($is_update ? 'true' : 'false'));

            // Insert broker record
            $this->db->insert('tblPartyBrokerMaster', $broker_insert);

            $affected = $this->db->affected_rows();
            log_message('debug', 'Broker master insert affected rows: ' . $affected);

            if ($affected > 0) {
                log_message('debug', 'Broker master data inserted successfully for AccountID: ' . $account_id . ' - BrokerID: ' . $broker_id . ' - BrokerContactID: ' . $broker_contact_id);
            } else {
                log_message('error', 'Failed to insert broker master data for AccountID: ' . $account_id . ' - Broker data: ' . json_encode($broker_insert) . ' - Last DB Error: ' . $this->db->error()['message']);
            }
        } catch (Exception $e) {
            log_message('error', 'Error inserting broker master data into tblPartyBrokerMaster: ' . $e->getMessage() . ' - Stack: ' . $e->getTraceAsString());
        }
    }

    /**

     * Update data in tblclients table

     * @param  array $form_data Form POST data

     * @param  integer $userid User ID

     * @return mixed boolean

     */

    public function update_tblclients($form_data, $userid = 0)
    {
//   $this->db->truncate('tblclients');
//         $this->db->truncate('tblBankMaster');
//         $this->db->truncate('tblcontacts');
//         $this->db->truncate('tblclientwiseshippingdata');
//         die; 

        // Map form fields to database column names - only non-null values

        $data = [

            'company' => isset($form_data['AccoountName']) ? $form_data['AccoountName'] : null,

            'FavouringName' => isset($form_data['favouring_name']) ? $form_data['favouring_name'] : null,

            'PAN' => isset($form_data['Pan']) ? $form_data['Pan'] : null,

            'GSTIN' => isset($form_data['vat']) ? $form_data['vat'] : null,

            'OrganisationType' => isset($form_data['organisation_type']) ? $form_data['organisation_type'] : null,

            'GSTType' => isset($form_data['gsttype']) ? $form_data['gsttype'] : null,

            'billing_country' => isset($form_data['country']) ? $form_data['country'] : null,

            'billing_state' => isset($form_data['state']) ? $form_data['state'] : null,

            'billing_city' => isset($form_data['city']) ? $form_data['city'] : null,

            'billing_zip' => isset($form_data['zip']) ? $form_data['zip'] : null,

            'billing_address' => isset($form_data['address']) ? $form_data['address'] : null,

            'MobileNo' => isset($form_data['phonenumber']) ? $form_data['phonenumber'] : null,

            'AltMobileNo' => isset($form_data['altphonenumber']) ? $form_data['altphonenumber'] : null,

            'Email' => isset($form_data['email']) ? $form_data['email'] : null,

            'IsTDS' => isset($form_data['Tds']) ? ($form_data['Tds'] == 1 ? 'Y' : 'N') : null,

            'TDSSection' => isset($form_data['Tdsselection']) ? $form_data['Tdsselection'] : null,

            'TDSPer' => isset($form_data['TdsPercent']) ? $form_data['TdsPercent'] : null,

            'PaymentTerms' => isset($form_data['payment_terms']) ? $form_data['payment_terms'] : null,

            'PaymentCycleType' => isset($form_data['payment_cycle_type']) ? $form_data['payment_cycle_type'] : null,

            'PaymentCycle' => isset($form_data['payment_cycle']) ? $form_data['payment_cycle'] : null,

            'GraceDay' => isset($form_data['credit_days']) ? $form_data['credit_days'] : null,

            'CreditLimit' => isset($form_data['MaxCrdAmt']) ? $form_data['MaxCrdAmt'] : null,

            'FreightTerms' => isset($form_data['freight_term']) ? $form_data['freight_term'] : null,

            'IsActive' => isset($form_data['Blockyn']) ? $form_data['Blockyn'] : null,

            'ActSubGroupID2' => isset($form_data['groups_in']) ? $form_data['groups_in'] : null,

            'DistributorType' => isset($form_data['groups_in']) ? $form_data['groups_in'] : null,

            'DeActiveReason' => isset($form_data['blocked_reason']) ? $form_data['blocked_reason'] : null,

            'TAN' => isset($form_data['tan_number']) ? $form_data['tan_number'] : null,

            'PriorityID' => isset($form_data['priority']) ? $form_data['priority'] : null,

            'FSSAINo' => isset($form_data['FLNO1']) ? $form_data['FLNO1'] : null,

            'FSSAIExpiry' => isset($form_data['expiry_licence']) ? $form_data['expiry_licence'] : null,

            'TerritoryID' => isset($form_data['territory']) ? $form_data['territory'] : null,

            'website' => isset($form_data['website']) ? $form_data['website'] : null,

            'Attachment' => isset($form_data['attachment']) ? $form_data['attachment'] : (isset($form_data['Attachment']) ? $form_data['Attachment'] : null),

            'AdditionalInfo' => isset($form_data['additional_info']) ? $form_data['additional_info'] : null,

            'longitude' => isset($form_data['longitude']) ? $form_data['longitude'] : null,

            'latitude' => isset($form_data['latitude']) ? $form_data['latitude'] : null,

            'default_language' => isset($form_data['default_language']) ? $form_data['default_language'] : null,

            'default_currency' => isset($form_data['default_currency']) ? $form_data['default_currency'] : null,

            'Lupdate' => date('Y-m-d H:i:s'),

        ];



        // Filter out null values

        $update_data = array_filter($data, function ($value) {

            return $value !== null;

        });

        // Convert date fields from DD/MM/YYYY to YYYY-MM-DD format
        if (isset($update_data['FSSAIExpiry']) && !empty($update_data['FSSAIExpiry'])) {
            $original_date = $update_data['FSSAIExpiry'];
            $converted_date = $this->convert_date_format($update_data['FSSAIExpiry']);
            log_message('debug', 'update_tblclients FSSAIExpiry conversion: Original=' . $original_date . ', Converted=' . ($converted_date ? $converted_date : 'NULL'));
            if ($converted_date !== null) {
                $update_data['FSSAIExpiry'] = $converted_date;
            } else {
                // If conversion failed, remove the field to avoid storing invalid date
                unset($update_data['FSSAIExpiry']);
            }
        }

        if (empty($update_data)) {

            return false;

        }



        try {

            $this->db->where('userid', $userid);

            $this->db->update('tblclients', $update_data);


            // Handle contact data update into tblcontacts
            if (isset($form_data['ContactData']) && !empty($form_data['ContactData']) && isset($form_data['AccountID'])) {
                // Insert new contacts (with UPDATE mode - deletes old ones first)
                $plant_id = isset($form_data['PlantID']) ? $form_data['PlantID'] : null;
                $this->insert_contacts_into_tblcontacts($form_data['ContactData'], $form_data['AccountID'], $plant_id, $userid, true);
            }

            // Handle shipping data update into tblclientwiseshippingdata
            if (isset($form_data['ShippingData']) && !empty($form_data['ShippingData']) && isset($form_data['AccountID'])) {
                // Insert new shipping locations (with UPDATE mode - deletes old ones first)
                $this->insert_shipping_data_into_tblclientwiseshippingdata($form_data['ShippingData'], $form_data['AccountID'], $userid, true);
            }

            // Handle broker master data update into tblPartyBrokerMaster
            if (isset($form_data['AccountID'])) {
                // Insert new broker data if provided (broker_person is optional)
                if (isset($form_data['broker']) && !empty($form_data['broker'])) {
                    $broker_contact_id = (isset($form_data['broker_person']) && !empty($form_data['broker_person'])) ? $form_data['broker_person'] : 0;
                    $plant_id = isset($form_data['PlantID']) ? $form_data['PlantID'] : null;
                    $this->insert_broker_master_data($form_data['AccountID'], $form_data['broker'], $broker_contact_id, $plant_id, $userid, $userid, true);
                }
            }

            // Handle bank data update into tblBankMaster
            if (isset($form_data['is_bank_detail']) && $form_data['is_bank_detail'] && isset($form_data['AccountID'])) {
                // Update bank data (with UPDATE mode - deletes old ones first)
                $this->insert_or_update_tblBankMaster($form_data['AccountID'], $form_data, $userid, $userid, true);
            }

            if ($this->db->affected_rows() > 0) {

                return true;

            }

        } catch (Exception $e) {

            // Log the error for debugging

            log_message('error', 'tblclients update error: ' . $e->getMessage());

            return false;

        }



        return false;

    }



    /**
     * Insert or Update bank master data into tblBankMaster table
     * @param  string $account_id Account ID
     * @param  array $bank_data Bank data array
     * @param  int $user_id User ID
     * @param  int $created_by Created By User ID (for new records)
     * @param  boolean $is_update If true, deletes old records first (UPDATE mode), else appends (INSERT mode)
     * @return void
     */
    private function insert_or_update_tblBankMaster($account_id, $bank_data, $user_id, $created_by, $is_update = false)
    {
        try {
            // If UPDATE mode, delete existing bank records first
            if ($is_update === true) {
                $this->db->where('AccountID', $account_id);
                $this->db->delete('tblBankMaster');
                log_message('debug', 'Deleted existing bank data for AccountID: ' . $account_id);
            }

            $bank = [
                'PlantID' => isset($bank_data['PlantID']) ? $bank_data['PlantID'] : null,
                'AccountID' => $account_id,
                'IFSC' => isset($bank_data['ifsc_code']) ? $bank_data['ifsc_code'] : null,
                'BankName' => isset($bank_data['bank_name']) ? $bank_data['bank_name'] : null,
                'BranchName' => isset($bank_data['branch_name']) ? $bank_data['branch_name'] : null,
                'BankAddress' => isset($bank_data['bank_address']) ? $bank_data['bank_address'] : null,
                'AccountNo' => isset($bank_data['account_number']) ? $bank_data['account_number'] : null,
                'HolderName' => isset($bank_data['account_holder_name']) ? $bank_data['account_holder_name'] : null,
                'UserID' => $user_id,
                'TransDate' => date('Y-m-d H:i:s'),
                'UserID2' => $created_by,
                'Lupdate' => date('Y-m-d H:i:s'),
            ];

            // Remove null/empty values before insert
            $bank_insert = array_filter($bank, function ($v) {
                return $v !== null && $v !== '';
            });

            log_message('debug', 'Bank master insert data: ' . json_encode($bank_insert) . ' - Update Mode: ' . ($is_update ? 'true' : 'false'));

            if (!empty($bank_insert)) {
                $this->db->insert('tblBankMaster', $bank_insert);
                if ($this->db->affected_rows() == 0) {
                    log_message('error', 'tblBankMaster insert failed: ' . json_encode($bank_insert));
                } else {
                    log_message('debug', 'tblBankMaster inserted for AccountID: ' . $account_id);
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error inserting/updating bank master data into tblBankMaster: ' . $e->getMessage() . ' - Stack: ' . $e->getTraceAsString());
        }
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update client informations
     */

    public function update($data, $id, $client_request = false)
    {

        $updated = false;

        $data = $this->check_zero_columns($data);



        $data = hooks()->apply_filters('before_client_updated', $data, $id);



        $update_all_other_transactions = (bool) Arr::pull($data, 'update_all_other_transactions');

        $update_credit_notes = (bool) Arr::pull($data, 'update_credit_notes');

        $custom_fields = Arr::pull($data, 'custom_fields') ?? [];

        $groups_in = Arr::pull($data, 'groups_in') ?? false;



        if (handle_custom_fields_post($id, $custom_fields)) {

            $updated = true;

        }



        $this->db->where('userid', $id);

        $this->db->update(db_prefix() . 'clients', $data);



        if ($this->db->affected_rows() > 0) {

            $updated = true;

        }



        if ($update_all_other_transactions || $update_credit_notes) {

            $transactions_update = [

                'billing_street' => $data['billing_street'],

                'billing_city' => $data['billing_city'],

                'billing_state' => $data['billing_state'],

                'billing_zip' => $data['billing_zip'],

                'billing_country' => $data['billing_country'],

                'shipping_street' => $data['shipping_street'],

                'shipping_city' => $data['shipping_city'],

                'shipping_state' => $data['shipping_state'],

                'shipping_zip' => $data['shipping_zip'],

                'shipping_country' => $data['shipping_country'],

            ];



            if ($update_all_other_transactions) {

                // Update all invoices except paid ones.

                $this->db->where('clientid', $id)

                    ->where('status !=', 2)

                    ->update('invoices', $transactions_update);



                if ($this->db->affected_rows() > 0) {

                    $updated = true;

                }



                // Update all estimates

                $this->db->where('clientid', $id)

                    ->update('estimates', $transactions_update);

                if ($this->db->affected_rows() > 0) {

                    $updated = true;

                }

            }



            if ($update_credit_notes) {

                $this->db->where('clientid', $id)

                    ->where('status !=', 2)

                    ->update('creditnotes', $transactions_update);



                if ($this->db->affected_rows() > 0) {

                    $updated = true;

                }

            }

        }



        if ($this->client_groups_model->sync_customer_groups($id, $groups_in)) {

            $updated = true;

        }



        do_action_deprecated('after_client_updated', [$id], '2.9.4', 'client_updated');



        hooks()->do_action('client_updated', [

            'id' => $id,

            'data' => $data,

            'update_all_other_transactions' => $update_all_other_transactions,

            'update_credit_notes' => $update_credit_notes,

            'custom_fields' => $custom_fields,

            'groups_in' => $groups_in,

            'updated' => &$updated,

        ]);



        if ($updated) {

            log_activity('Customer Info Updated [ID: ' . $id . ']');

        }



        return $updated;

    }



    /**

     * Update contact data

     * @param  array  $data           $_POST data

     * @param  mixed  $id             contact id

     * @param  boolean $client_request is request from customers area

     * @return mixed

     */

    public function update_contact($data, $id, $client_request = false)
    {

        $affectedRows = 0;

        $contact = $this->get_contact($id);

        if (empty($data['password'])) {

            unset($data['password']);

        } else {

            $data['password'] = app_hash_password($data['password']);

            $data['last_password_change'] = date('Y-m-d H:i:s');

        }



        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;

        $set_password_email_sent = false;



        $permissions = isset($data['permissions']) ? $data['permissions'] : [];

        $data['is_primary'] = isset($data['is_primary']) ? 1 : 0;



        // Contact cant change if is primary or not

        if ($client_request == true) {

            unset($data['is_primary']);

        }



        if (isset($data['custom_fields'])) {

            $custom_fields = $data['custom_fields'];

            if (handle_custom_fields_post($id, $custom_fields)) {

                $affectedRows++;

            }

            unset($data['custom_fields']);

        }



        if ($client_request == false) {

            $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 : 0;

            $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 : 0;

            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 : 0;

            $data['contract_emails'] = isset($data['contract_emails']) ? 1 : 0;

            $data['task_emails'] = isset($data['task_emails']) ? 1 : 0;

            $data['project_emails'] = isset($data['project_emails']) ? 1 : 0;

            $data['ticket_emails'] = isset($data['ticket_emails']) ? 1 : 0;

        }



        $data = hooks()->apply_filters('before_update_contact', $data, $id);



        $this->db->where('id', $id);

        $this->db->update(db_prefix() . 'contacts', $data);



        if ($this->db->affected_rows() > 0) {

            $affectedRows++;

            if (isset($data['is_primary']) && $data['is_primary'] == 1) {

                $this->db->where('userid', $contact->userid);

                $this->db->where('id !=', $id);

                $this->db->update(db_prefix() . 'contacts', [

                    'is_primary' => 0,

                ]);

            }

        }



        if ($client_request == false) {

            $customer_permissions = $this->roles_model->get_contact_permissions($id);

            if (sizeof($customer_permissions) > 0) {

                foreach ($customer_permissions as $customer_permission) {

                    if (!in_array($customer_permission['permission_id'], $permissions)) {

                        $this->db->where('userid', $id);

                        $this->db->where('permission_id', $customer_permission['permission_id']);

                        $this->db->delete(db_prefix() . 'contact_permissions');

                        if ($this->db->affected_rows() > 0) {

                            $affectedRows++;

                        }

                    }

                }

                foreach ($permissions as $permission) {

                    $this->db->where('userid', $id);

                    $this->db->where('permission_id', $permission);

                    $_exists = $this->db->get(db_prefix() . 'contact_permissions')->row();

                    if (!$_exists) {

                        $this->db->insert(db_prefix() . 'contact_permissions', [

                            'userid' => $id,

                            'permission_id' => $permission,

                        ]);

                        if ($this->db->affected_rows() > 0) {

                            $affectedRows++;

                        }

                    }

                }

            } else {

                foreach ($permissions as $permission) {

                    $this->db->insert(db_prefix() . 'contact_permissions', [

                        'userid' => $id,

                        'permission_id' => $permission,

                    ]);

                    if ($this->db->affected_rows() > 0) {

                        $affectedRows++;

                    }

                }

            }

            if ($send_set_password_email) {

                $set_password_email_sent = $this->authentication_model->set_password_email($data['email'], 0);

            }

        }



        if (($client_request == true) && $send_set_password_email) {

            $set_password_email_sent = $this->authentication_model->set_password_email($data['email'], 0);

        }



        if ($affectedRows > 0) {

            hooks()->do_action('contact_updated', $id, $data);

        }



        if ($affectedRows > 0 && !$set_password_email_sent) {

            log_activity('Contact Updated [ID: ' . $id . ']');



            return true;

        } elseif ($affectedRows > 0 && $set_password_email_sent) {

            return [

                'set_password_email_sent_and_profile_updated' => true,

            ];

        } elseif ($affectedRows == 0 && $set_password_email_sent) {

            return [

                'set_password_email_sent' => true,

            ];

        }



        return false;

    }



    /**

     * Add new contact

     * @param array  $data               $_POST data

     * @param mixed  $customer_id        customer id

     * @param boolean $not_manual_request is manual from admin area customer profile or register, convert to lead

     */

    public function add_contact($data, $customer_id, $not_manual_request = false)
    {

        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;



        if (isset($data['custom_fields'])) {

            $custom_fields = $data['custom_fields'];

            unset($data['custom_fields']);

        }



        if (isset($data['permissions'])) {

            $permissions = $data['permissions'];

            unset($data['permissions']);

        }



        $data['email_verified_at'] = date('Y-m-d H:i:s');



        $send_welcome_email = true;



        if (isset($data['donotsendwelcomeemail'])) {

            $send_welcome_email = false;

        }



        if (defined('CONTACT_REGISTERING')) {

            $send_welcome_email = true;



            // Do not send welcome email if confirmation for registration is enabled

            if (get_option('customers_register_require_confirmation') == '1') {

                $send_welcome_email = false;

            }



            // If client register set this contact as primary

            $data['is_primary'] = 1;



            if (is_email_verification_enabled() && !empty($data['email'])) {

                // Verification is required on register

                $data['email_verified_at'] = null;

                $data['email_verification_key'] = app_generate_hash();

            }

        }



        if (isset($data['is_primary'])) {

            $data['is_primary'] = 1;

            $this->db->where('userid', $customer_id);

            $this->db->update(db_prefix() . 'contacts', [

                'is_primary' => 0,

            ]);

        } else {

            $data['is_primary'] = 0;

        }



        $password_before_hash = '';

        $data['userid'] = $customer_id;

        if (isset($data['password'])) {

            $password_before_hash = $data['password'];

            $data['password'] = app_hash_password($data['password']);

        }



        $data['datecreated'] = date('Y-m-d H:i:s');



        if (!$not_manual_request) {

            $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 : 0;

            $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 : 0;

            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 : 0;

            $data['contract_emails'] = isset($data['contract_emails']) ? 1 : 0;

            $data['task_emails'] = isset($data['task_emails']) ? 1 : 0;

            $data['project_emails'] = isset($data['project_emails']) ? 1 : 0;

            $data['ticket_emails'] = isset($data['ticket_emails']) ? 1 : 0;

        }



        $data['email'] = trim($data['email']);



        $data = hooks()->apply_filters('before_create_contact', $data);



        $this->db->insert(db_prefix() . 'contacts', $data);

        $contact_id = $this->db->insert_id();



        if ($contact_id) {

            if (isset($custom_fields)) {

                handle_custom_fields_post($contact_id, $custom_fields);

            }

            // request from admin area

            if (!isset($permissions) && $not_manual_request == false) {

                $permissions = [];

            } elseif ($not_manual_request == true) {

                $permissions = [];

                $_permissions = get_contact_permissions();

                $default_permissions = @unserialize(get_option('default_contact_permissions'));

                if (is_array($default_permissions)) {

                    foreach ($_permissions as $permission) {

                        if (in_array($permission['id'], $default_permissions)) {

                            array_push($permissions, $permission['id']);

                        }

                    }

                }

            }



            if ($not_manual_request == true) {

                // update all email notifications to 0

                $this->db->where('id', $contact_id);

                $this->db->update(db_prefix() . 'contacts', [

                    'invoice_emails' => 0,

                    'estimate_emails' => 0,

                    'credit_note_emails' => 0,

                    'contract_emails' => 0,

                    'task_emails' => 0,

                    'project_emails' => 0,

                    'ticket_emails' => 0,

                ]);

            }

            foreach ($permissions as $permission) {

                $this->db->insert(db_prefix() . 'contact_permissions', [

                    'userid' => $contact_id,

                    'permission_id' => $permission,

                ]);



                // Auto set email notifications based on permissions

                if ($not_manual_request == true) {

                    if ($permission == 6) {

                        $this->db->where('id', $contact_id);

                        $this->db->update(db_prefix() . 'contacts', ['project_emails' => 1, 'task_emails' => 1]);

                    } elseif ($permission == 3) {

                        $this->db->where('id', $contact_id);

                        $this->db->update(db_prefix() . 'contacts', ['contract_emails' => 1]);

                    } elseif ($permission == 2) {

                        $this->db->where('id', $contact_id);

                        $this->db->update(db_prefix() . 'contacts', ['estimate_emails' => 1]);

                    } elseif ($permission == 1) {

                        $this->db->where('id', $contact_id);

                        $this->db->update(db_prefix() . 'contacts', ['invoice_emails' => 1, 'credit_note_emails' => 1]);

                    } elseif ($permission == 5) {

                        $this->db->where('id', $contact_id);

                        $this->db->update(db_prefix() . 'contacts', ['ticket_emails' => 1]);

                    }

                }

            }



            if ($send_welcome_email == true && !empty($data['email'])) {

                send_mail_template(

                    'customer_created_welcome_mail',

                    $data['email'],

                    $data['userid'],

                    $contact_id,

                    $password_before_hash

                );

            }



            if ($send_set_password_email) {

                $this->authentication_model->set_password_email($data['email'], 0);

            }



            if (defined('CONTACT_REGISTERING')) {

                $this->send_verification_email($contact_id);

            } else {

                // User already verified because is added from admin area, try to transfer any tickets

                $this->load->model('tickets_model');

                $this->tickets_model->transfer_email_tickets_to_contact($data['email'], $contact_id);

            }



            log_activity('Contact Created [ID: ' . $contact_id . ']');



            hooks()->do_action('contact_created', $contact_id);



            return $contact_id;

        }



        return false;

    }



    /**

     * Add new contact via customers area

     *

     * @param array  $data

     * @param mixed  $customer_id

     */

    public function add_contact_via_customers_area($data, $customer_id)
    {

        $send_welcome_email = isset($data['donotsendwelcomeemail']) && $data['donotsendwelcomeemail'] ? false : true;

        $send_set_password_email = isset($data['send_set_password_email']) && $data['send_set_password_email'] ? true : false;

        $custom_fields = $data['custom_fields'];

        unset($data['custom_fields']);



        if (!is_email_verification_enabled()) {

            $data['email_verified_at'] = date('Y-m-d H:i:s');

        } elseif (is_email_verification_enabled() && !empty($data['email'])) {

            // Verification is required on register

            $data['email_verified_at'] = null;

            $data['email_verification_key'] = app_generate_hash();

        }



        $password_before_hash = $data['password'];



        $data = array_merge($data, [

            'datecreated' => date('Y-m-d H:i:s'),

            'userid' => $customer_id,

            'password' => app_hash_password(isset($data['password']) ? $data['password'] : time()),

        ]);



        $data = hooks()->apply_filters('before_create_contact', $data);

        $this->db->insert(db_prefix() . 'contacts', $data);



        $contact_id = $this->db->insert_id();



        if ($contact_id) {

            handle_custom_fields_post($contact_id, $custom_fields);



            // Apply default permissions

            $default_permissions = @unserialize(get_option('default_contact_permissions'));



            if (is_array($default_permissions)) {

                foreach (get_contact_permissions() as $permission) {

                    if (in_array($permission['id'], $default_permissions)) {

                        $this->db->insert(db_prefix() . 'contact_permissions', [

                            'userid' => $contact_id,

                            'permission_id' => $permission['id'],

                        ]);

                    }

                }

            }



            if ($send_welcome_email === true) {

                send_mail_template(

                    'customer_created_welcome_mail',

                    $data['email'],

                    $customer_id,

                    $contact_id,

                    $password_before_hash

                );

            }



            if ($send_set_password_email === true) {

                $this->authentication_model->set_password_email($data['email'], 0);

            }



            log_activity('Contact Created [ID: ' . $contact_id . ']');

            hooks()->do_action('contact_created', $contact_id);



            return $contact_id;

        }



        return false;

    }



    /**

     * Used to update company details from customers area

     * @param  array $data $_POST data

     * @param  mixed $id

     * @return boolean

     */

    public function update_company_details($data, $id)
    {

        $affectedRows = 0;

        if (isset($data['custom_fields'])) {

            $custom_fields = $data['custom_fields'];

            if (handle_custom_fields_post($id, $custom_fields)) {

                $affectedRows++;

            }

            unset($data['custom_fields']);

        }

        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {

            $data['country'] = 0;

        }

        if (isset($data['billing_country']) && $data['billing_country'] == '') {

            $data['billing_country'] = 0;

        }

        if (isset($data['shipping_country']) && $data['shipping_country'] == '') {

            $data['shipping_country'] = 0;

        }



        // From v.1.9.4 these fields are textareas

        $data['address'] = trim($data['address']);

        $data['address'] = nl2br($data['address']);

        if (isset($data['billing_street'])) {

            $data['billing_street'] = trim($data['billing_street']);

            $data['billing_street'] = nl2br($data['billing_street']);

        }

        if (isset($data['shipping_street'])) {

            $data['shipping_street'] = trim($data['shipping_street']);

            $data['shipping_street'] = nl2br($data['shipping_street']);

        }



        $data = hooks()->apply_filters('customer_update_company_info', $data, $id);



        $this->db->where('userid', $id);

        $this->db->update(db_prefix() . 'clients', $data);

        if ($this->db->affected_rows() > 0) {

            $affectedRows++;

        }

        if ($affectedRows > 0) {

            hooks()->do_action('customer_updated_company_info', $id);

            log_activity('Customer Info Updated From Clients Area [ID: ' . $id . ']');



            return true;

        }



        return false;

    }



    /**

     * Get customer staff members that are added as customer admins

     * @param  mixed $id customer id

     * @return array

     */

    public function get_admins($id)
    {

        $this->db->where('customer_id', $id);



        return $this->db->get(db_prefix() . 'customer_admins')->result_array();

    }



    /**

     * Get unique staff id's of customer admins

     * @return array

     */

    public function get_customers_admin_unique_ids()
    {

        return $this->db->query('SELECT DISTINCT(staff_id) FROM ' . db_prefix() . 'customer_admins')->result_array();

    }



    /**

     * Assign staff members as admin to customers

     * @param  array $data $_POST data

     * @param  mixed $id   customer id

     * @return boolean

     */

    public function assign_admins($data, $id)
    {

        $affectedRows = 0;



        if (count($data) == 0) {

            $this->db->where('customer_id', $id);

            $this->db->delete(db_prefix() . 'customer_admins');

            if ($this->db->affected_rows() > 0) {

                $affectedRows++;

            }

        } else {

            $current_admins = $this->get_admins($id);

            $current_admins_ids = [];

            foreach ($current_admins as $c_admin) {

                array_push($current_admins_ids, $c_admin['staff_id']);

            }

            foreach ($current_admins_ids as $c_admin_id) {

                if (!in_array($c_admin_id, $data['customer_admins'])) {

                    $this->db->where('staff_id', $c_admin_id);

                    $this->db->where('customer_id', $id);

                    $this->db->delete(db_prefix() . 'customer_admins');

                    if ($this->db->affected_rows() > 0) {

                        $affectedRows++;

                    }

                }

            }

            foreach ($data['customer_admins'] as $n_admin_id) {

                if (
                    total_rows(db_prefix() . 'customer_admins', [

                        'customer_id' => $id,

                        'staff_id' => $n_admin_id,

                    ]) == 0
                ) {

                    $this->db->insert(db_prefix() . 'customer_admins', [

                        'customer_id' => $id,

                        'staff_id' => $n_admin_id,

                        'date_assigned' => date('Y-m-d H:i:s'),

                    ]);

                    if ($this->db->affected_rows() > 0) {

                        $affectedRows++;

                    }

                }

            }

        }

        if ($affectedRows > 0) {

            return true;

        }



        return false;

    }



    /**

     * @param  integer ID

     * @return boolean

     * Delete client, also deleting rows from, dismissed client announcements, ticket replies, tickets, autologin, user notes

     */

    public function delete($id)
    {

        $affectedRows = 0;



        if (!is_gdpr() && is_reference_in_table('clientid', db_prefix() . 'invoices', $id)) {

            return [

                'referenced' => true,

            ];

        }



        if (!is_gdpr() && is_reference_in_table('clientid', db_prefix() . 'estimates', $id)) {

            return [

                'referenced' => true,

            ];

        }



        if (!is_gdpr() && is_reference_in_table('clientid', db_prefix() . 'creditnotes', $id)) {

            return [

                'referenced' => true,

            ];

        }



        hooks()->do_action('before_client_deleted', $id);



        $last_activity = get_last_system_activity_id();

        $company = get_company_name($id);



        $this->db->where('userid', $id);

        $this->db->delete(db_prefix() . 'clients');

        if ($this->db->affected_rows() > 0) {

            $affectedRows++;

            // Delete all user contacts

            $this->db->where('userid', $id);

            $contacts = $this->db->get(db_prefix() . 'contacts')->result_array();

            foreach ($contacts as $contact) {

                $this->delete_contact($contact['id']);

            }



            // Delete all tickets start here

            $this->db->where('userid', $id);

            $tickets = $this->db->get(db_prefix() . 'tickets')->result_array();

            $this->load->model('tickets_model');

            foreach ($tickets as $ticket) {

                $this->tickets_model->delete($ticket['ticketid']);

            }



            $this->db->where('rel_id', $id);

            $this->db->where('rel_type', 'customer');

            $this->db->delete(db_prefix() . 'notes');



            if (is_gdpr() && get_option('gdpr_on_forgotten_remove_invoices_credit_notes') == '1') {

                $this->load->model('invoices_model');

                $this->db->where('clientid', $id);

                $invoices = $this->db->get(db_prefix() . 'invoices')->result_array();

                foreach ($invoices as $invoice) {

                    $this->invoices_model->delete($invoice['id'], true);

                }



                $this->load->model('credit_notes_model');

                $this->db->where('clientid', $id);

                $credit_notes = $this->db->get(db_prefix() . 'creditnotes')->result_array();

                foreach ($credit_notes as $credit_note) {

                    $this->credit_notes_model->delete($credit_note['id'], true);

                }

            } elseif (is_gdpr()) {

                $this->db->where('clientid', $id);

                $this->db->update(db_prefix() . 'invoices', ['deleted_customer_name' => $company]);



                $this->db->where('clientid', $id);

                $this->db->update(db_prefix() . 'creditnotes', ['deleted_customer_name' => $company]);

            }



            $this->db->where('clientid', $id);

            $this->db->update(db_prefix() . 'creditnotes', [

                'clientid' => 0,

                'project_id' => 0,

            ]);



            $this->db->where('clientid', $id);

            $this->db->update(db_prefix() . 'invoices', [

                'clientid' => 0,

                'recurring' => 0,

                'recurring_type' => null,

                'custom_recurring' => 0,

                'cycles' => 0,

                'last_recurring_date' => null,

                'project_id' => 0,

                'subscription_id' => 0,

                'cancel_overdue_reminders' => 1,

                'last_overdue_reminder' => null,

                'last_due_reminder' => null,

            ]);



            if (is_gdpr() && get_option('gdpr_on_forgotten_remove_estimates') == '1') {

                $this->load->model('estimates_model');

                $this->db->where('clientid', $id);

                $estimates = $this->db->get(db_prefix() . 'estimates')->result_array();

                foreach ($estimates as $estimate) {

                    $this->estimates_model->delete($estimate['id'], true);

                }

            } elseif (is_gdpr()) {

                $this->db->where('clientid', $id);

                $this->db->update(db_prefix() . 'estimates', ['deleted_customer_name' => $company]);

            }



            $this->db->where('clientid', $id);

            $this->db->update(db_prefix() . 'estimates', [

                'clientid' => 0,

                'project_id' => 0,

                'is_expiry_notified' => 1,

            ]);



            $this->load->model('subscriptions_model');

            $this->db->where('clientid', $id);

            $subscriptions = $this->db->get(db_prefix() . 'subscriptions')->result_array();

            foreach ($subscriptions as $subscription) {

                $this->subscriptions_model->delete($subscription['id'], true);

            }

            // Get all client contracts

            $this->load->model('contracts_model');

            $this->db->where('client', $id);

            $contracts = $this->db->get(db_prefix() . 'contracts')->result_array();

            foreach ($contracts as $contract) {

                $this->contracts_model->delete($contract['id']);

            }

            // Delete the custom field values

            $this->db->where('relid', $id);

            $this->db->where('fieldto', 'customers');

            $this->db->delete(db_prefix() . 'customfieldsvalues');



            // Get customer related tasks

            $this->db->where('rel_type', 'customer');

            $this->db->where('rel_id', $id);

            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();



            foreach ($tasks as $task) {

                $this->tasks_model->delete_task($task['id'], false);

            }



            $this->db->where('rel_type', 'customer');

            $this->db->where('rel_id', $id);

            $this->db->delete(db_prefix() . 'reminders');



            $this->db->where('customer_id', $id);

            $this->db->delete(db_prefix() . 'customer_admins');



            $this->db->where('customer_id', $id);

            $this->db->delete(db_prefix() . 'vault');



            $this->db->where('customer_id', $id);

            $this->db->delete(db_prefix() . 'customer_groups');



            $this->load->model('proposals_model');

            $this->db->where('rel_id', $id);

            $this->db->where('rel_type', 'customer');

            $proposals = $this->db->get(db_prefix() . 'proposals')->result_array();

            foreach ($proposals as $proposal) {

                $this->proposals_model->delete($proposal['id']);

            }

            $this->db->where('rel_id', $id);

            $this->db->where('rel_type', 'customer');

            $attachments = $this->db->get(db_prefix() . 'files')->result_array();

            foreach ($attachments as $attachment) {

                $this->delete_attachment($attachment['id']);

            }



            $this->db->where('clientid', $id);

            $expenses = $this->db->get(db_prefix() . 'expenses')->result_array();



            $this->load->model('expenses_model');

            foreach ($expenses as $expense) {

                $this->expenses_model->delete($expense['id'], true);

            }



            $this->db->where('client_id', $id);

            $this->db->delete(db_prefix() . 'user_meta');



            $this->db->where('client_id', $id);

            $this->db->update(db_prefix() . 'leads', ['client_id' => 0]);



            // Delete all projects

            $this->load->model('projects_model');

            $this->db->where('clientid', $id);

            $projects = $this->db->get(db_prefix() . 'projects')->result_array();

            foreach ($projects as $project) {

                $this->projects_model->delete($project['id']);

            }

        }

        if ($affectedRows > 0) {

            hooks()->do_action('after_client_deleted', $id);



            // Delete activity log caused by delete customer function

            if ($last_activity) {

                $this->db->where('id >', $last_activity->id);

                $this->db->delete(db_prefix() . 'activity_log');

            }



            log_activity('Client Deleted [ID: ' . $id . ']');



            return true;

        }



        return false;

    }



    /**

     * Delete customer contact

     * @param  mixed $id contact id

     * @return boolean

     */

    public function delete_contact($id)
    {

        hooks()->do_action('before_delete_contact', $id);



        $this->db->where('id', $id);

        $result = $this->db->get(db_prefix() . 'contacts')->row();

        $customer_id = $result->userid;



        $last_activity = get_last_system_activity_id();



        $this->db->where('id', $id);

        $this->db->delete(db_prefix() . 'contacts');



        if ($this->db->affected_rows() > 0) {

            if (is_dir(get_upload_path_by_type('contact_profile_images') . $id)) {

                delete_dir(get_upload_path_by_type('contact_profile_images') . $id);

            }



            $this->db->where('contact_id', $id);

            $this->db->delete(db_prefix() . 'consents');



            $this->db->where('contact_id', $id);

            $this->db->delete(db_prefix() . 'shared_customer_files');



            $this->db->where('userid', $id);

            $this->db->where('staff', 0);

            $this->db->delete(db_prefix() . 'dismissed_announcements');



            $this->db->where('relid', $id);

            $this->db->where('fieldto', 'contacts');

            $this->db->delete(db_prefix() . 'customfieldsvalues');



            $this->db->where('userid', $id);

            $this->db->delete(db_prefix() . 'contact_permissions');



            $this->db->where('userid', $id);
            $this->db->update('clients', [
                'IsActive' => $status,
            ]);
            $this->db->delete(db_prefix() . 'user_auto_login');



            $this->db->select('ticketid');

            $this->db->where('contactid', $id);

            $this->db->where('userid', $customer_id);

            $tickets = $this->db->get(db_prefix() . 'tickets')->result_array();



            $this->load->model('tickets_model');

            foreach ($tickets as $ticket) {

                $this->tickets_model->delete($ticket['ticketid']);

            }



            $this->load->model('tasks_model');



            $this->db->where('addedfrom', $id);

            $this->db->where('is_added_from_contact', 1);

            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();



            foreach ($tasks as $task) {

                $this->tasks_model->delete_task($task['id'], false);

            }



            // Added from contact in customer profile

            $this->db->where('contact_id', $id);

            $this->db->where('rel_type', 'customer');

            $attachments = $this->db->get(db_prefix() . 'files')->result_array();



            foreach ($attachments as $attachment) {

                $this->delete_attachment($attachment['id']);

            }



            // Remove contact files uploaded to tasks

            $this->db->where('rel_type', 'task');

            $this->db->where('contact_id', $id);

            $filesUploadedFromContactToTasks = $this->db->get(db_prefix() . 'files')->result_array();



            foreach ($filesUploadedFromContactToTasks as $file) {

                $this->tasks_model->remove_task_attachment($file['id']);

            }



            $this->db->where('contact_id', $id);

            $tasksComments = $this->db->get(db_prefix() . 'task_comments')->result_array();

            foreach ($tasksComments as $comment) {

                $this->tasks_model->remove_comment($comment['id'], true);

            }



            $this->load->model('projects_model');



            $this->db->where('contact_id', $id);

            $files = $this->db->get(db_prefix() . 'project_files')->result_array();

            foreach ($files as $file) {

                $this->projects_model->remove_file($file['id'], false);

            }



            $this->db->where('contact_id', $id);

            $discussions = $this->db->get(db_prefix() . 'projectdiscussions')->result_array();

            foreach ($discussions as $discussion) {

                $this->projects_model->delete_discussion($discussion['id'], false);

            }



            $this->db->where('contact_id', $id);

            $discussionsComments = $this->db->get(db_prefix() . 'projectdiscussioncomments')->result_array();

            foreach ($discussionsComments as $comment) {

                $this->projects_model->delete_discussion_comment($comment['id'], false);

            }



            $this->db->where('contact_id', $id);

            $this->db->delete(db_prefix() . 'user_meta');



            $this->db->where('(email="' . $result->email . '" OR bcc LIKE "%' . $result->email . '%" OR cc LIKE "%' . $result->email . '%")');

            $this->db->delete(db_prefix() . 'mail_queue');



            if (is_gdpr()) {

                if (table_exists('listemails')) {

                    $this->db->where('email', $result->email);

                    $this->db->delete(db_prefix() . 'listemails');

                }



                if (!empty($result->last_ip)) {

                    $this->db->where('ip', $result->last_ip);

                    $this->db->delete(db_prefix() . 'knowedge_base_article_feedback');

                }



                $this->db->where('email', $result->email);

                $this->db->delete(db_prefix() . 'tickets_pipe_log');



                $this->db->where('email', $result->email);

                $this->db->delete(db_prefix() . 'tracked_mails');



                $this->db->where('contact_id', $id);

                $this->db->delete(db_prefix() . 'project_activity');



                $this->db->where('(additional_data LIKE "%' . $result->email . '%" OR full_name LIKE "%' . $result->firstname . ' ' . $result->lastname . '%")');

                $this->db->where('additional_data != "" AND additional_data IS NOT NULL');

                $this->db->delete(db_prefix() . 'sales_activity');



                $contactActivityQuery = false;

                if (!empty($result->email)) {

                    $this->db->or_like('description', $result->email);

                    $contactActivityQuery = true;

                }

                if (!empty($result->firstname)) {

                    $this->db->or_like('description', $result->firstname);

                    $contactActivityQuery = true;

                }

                if (!empty($result->lastname)) {

                    $this->db->or_like('description', $result->lastname);

                    $contactActivityQuery = true;

                }



                if (!empty($result->phonenumber)) {

                    $this->db->or_like('description', $result->phonenumber);

                    $contactActivityQuery = true;

                }



                if (!empty($result->last_ip)) {

                    $this->db->or_like('description', $result->last_ip);

                    $contactActivityQuery = true;

                }



                if ($contactActivityQuery) {

                    $this->db->delete(db_prefix() . 'activity_log');

                }

            }



            // Delete activity log caused by delete contact function

            if ($last_activity) {

                $this->db->where('id >', $last_activity->id);

                $this->db->delete(db_prefix() . 'activity_log');

            }



            hooks()->do_action('contact_deleted', $id, $result);



            return true;

        }



        return false;

    }



    /**

     * Get customer default currency

     * @param  mixed $id customer id

     * @return mixed

     */

    public function get_customer_default_currency($id)
    {

        $this->db->select('default_currency');

        $this->db->where('userid', $id);

        $result = $this->db->get(db_prefix() . 'clients')->row();

        if ($result) {

            return $result->default_currency;

        }



        return false;

    }



    /**

     *  Get customer billing details

     * @param   mixed $id   customer id

     * @return  array

     */

    public function get_customer_billing_and_shipping_details($id)
    {

        $this->db->select('billing_street,billing_city,billing_state,billing_zip,billing_country,shipping_street,shipping_city,shipping_state,shipping_zip,shipping_country');

        $this->db->from(db_prefix() . 'clients');

        $this->db->where('userid', $id);



        $result = $this->db->get()->result_array();

        if (count($result) > 0) {

            $result[0]['billing_street'] = clear_textarea_breaks($result[0]['billing_street']);

            $result[0]['shipping_street'] = clear_textarea_breaks($result[0]['shipping_street']);

        }



        return $result;

    }



    /**

     * Get customer files uploaded in the customer profile

     * @param  mixed $id    customer id

     * @param  array  $where perform where

     * @return array

     */

    public function get_customer_files($id, $where = [])
    {

        $this->db->where($where);

        $this->db->where('rel_id', $id);

        $this->db->where('rel_type', 'customer');

        $this->db->order_by('dateadded', 'desc');



        return $this->db->get(db_prefix() . 'files')->result_array();

    }



    /**

     * Delete customer attachment uploaded from the customer profile

     * @param  mixed $id attachment id

     * @return boolean

     */

    public function delete_attachment($id)
    {

        $this->db->where('id', $id);

        $attachment = $this->db->get(db_prefix() . 'files')->row();

        $deleted = false;

        if ($attachment) {

            if (empty($attachment->external)) {

                $relPath = get_upload_path_by_type('customer') . $attachment->rel_id . '/';

                $fullPath = $relPath . $attachment->file_name;

                unlink($fullPath);

                $fname = pathinfo($fullPath, PATHINFO_FILENAME);

                $fext = pathinfo($fullPath, PATHINFO_EXTENSION);

                $thumbPath = $relPath . $fname . '_thumb.' . $fext;

                if (file_exists($thumbPath)) {

                    unlink($thumbPath);

                }

            }



            $this->db->where('id', $id);

            $this->db->delete(db_prefix() . 'files');

            if ($this->db->affected_rows() > 0) {

                $deleted = true;

                $this->db->where('file_id', $id);

                $this->db->delete(db_prefix() . 'shared_customer_files');

                log_activity('Customer Attachment Deleted [ID: ' . $attachment->rel_id . ']');

            }



            if (is_dir(get_upload_path_by_type('customer') . $attachment->rel_id)) {

                // Check if no attachments left, so we can delete the folder also

                $other_attachments = list_files(get_upload_path_by_type('customer') . $attachment->rel_id);

                if (count($other_attachments) == 0) {

                    delete_dir(get_upload_path_by_type('customer') . $attachment->rel_id);

                }

            }

        }



        return $deleted;

    }



    /**

     * @param  integer ID

     * @param  integer Status ID

     * @return boolean

     * Update contact status Active/Inactive

     */

    public function change_contact_status($id, $status)
    {

        $status = hooks()->apply_filters('change_contact_status', $status, $id);



        $this->db->where('id', $id);

        $this->db->update(db_prefix() . 'contacts', [

            'active' => $status,

        ]);

        if ($this->db->affected_rows() > 0) {

            hooks()->do_action('contact_status_changed', [

                'id' => $id,

                'status' => $status,

            ]);



            log_activity('Contact Status Changed [ContactID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');



            return true;

        }



        return false;

    }



    /**

     * @param  integer ID

     * @param  integer Status ID

     * @return boolean

     * Update client status Active/Inactive

     */

    public function change_client_status($id, $status)
    {

        $this->db->where('userid', $id);

        $this->db->update('clients', [

            'IsActive' => $status,

        ]);



        if ($this->db->affected_rows() > 0) {

            hooks()->do_action('client_status_changed', [

                'id' => $id,

                'status' => $status,

            ]);



            log_activity('Customer Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');



            return true;

        }



        return false;

    }



    /**

     * Change contact password, used from client area

     * @param  mixed $id          contact id to change password

     * @param  string $oldPassword old password to verify

     * @param  string $newPassword new password

     * @return boolean

     */

    public function change_contact_password($id, $oldPassword, $newPassword)
    {

        // Get current password

        $this->db->where('id', $id);

        $client = $this->db->get(db_prefix() . 'contacts')->row();



        if (!app_hasher()->CheckPassword($oldPassword, $client->password)) {

            return [

                'old_password_not_match' => true,

            ];

        }



        $this->db->where('id', $id);

        $this->db->update(db_prefix() . 'contacts', [

            'last_password_change' => date('Y-m-d H:i:s'),

            'password' => app_hash_password($newPassword),

        ]);



        if ($this->db->affected_rows() > 0) {

            log_activity('Contact Password Changed [ContactID: ' . $id . ']');



            return true;

        }



        return false;

    }



    /**

     * Get customer groups where customer belongs

     * @param  mixed $id customer id

     * @return array

     */

    public function get_customer_groups($id)
    {

        return $this->client_groups_model->get_customer_groups($id);

    }



    /**

     * Get all customer groups

     * @param  string $id

     * @return mixed

     */

    public function get_groups($id = ''): mixed
    {

        return $this->client_groups_model->get_groups($id);

    }



    /**

     * Delete customer groups

     * @param  mixed $id group id

     * @return boolean

     */

    public function delete_group($id)
    {

        return $this->client_groups_model->delete($id);

    }



    /**

     * Add new customer groups

     * @param array $data $_POST data

     */

    public function add_group($data)
    {

        return $this->client_groups_model->add($data);

    }



    /**

     * Edit customer group

     * @param  array $data $_POST data

     * @return boolean

     */

    public function edit_group($data)
    {

        return $this->client_groups_model->edit($data);

    }



    /**

    * Create new vault entry

    * @param  array $data        $_POST data

    * @param  mixed $customer_id customer id

    * @return boolean

    */

    public function vault_entry_create($data, $customer_id)
    {

        return $this->client_vault_entries_model->create($data, $customer_id);

    }



    /**

     * Update vault entry

     * @param  mixed $id   vault entry id

     * @param  array $data $_POST data

     * @return boolean

     */

    public function vault_entry_update($id, $data)
    {

        return $this->client_vault_entries_model->update($id, $data);

    }



    /**

     * Delete vault entry

     * @param  mixed $id entry id

     * @return boolean

     */

    public function vault_entry_delete($id)
    {

        return $this->client_vault_entries_model->delete($id);

    }



    /**

     * Get customer vault entries

     * @param  mixed $customer_id

     * @param  array  $where       additional wher

     * @return array

     */

    public function get_vault_entries($customer_id, $where = [])
    {

        return $this->client_vault_entries_model->get_by_customer_id($customer_id, $where);

    }



    /**

     * Get single vault entry

     * @param  mixed $id vault entry id

     * @return object

     */

    public function get_vault_entry($id)
    {

        return $this->client_vault_entries_model->get($id);

    }



    /**

    * Get customer statement formatted

    * @param  mixed $customer_id customer id

    * @param  string $from        date from

    * @param  string $to          date to

    * @return array

    */

    public function get_statement($customer_id, $from, $to)
    {

        return $this->statement_model->get_statement($customer_id, $from, $to);

    }



    /**

    * Send customer statement to email

    * @param  mixed $customer_id customer id

    * @param  array $send_to     array of contact emails to send

    * @param  string $from        date from

    * @param  string $to          date to

    * @param  string $cc          email CC

    * @return boolean

    */

    public function send_statement_to_email($customer_id, $send_to, $from, $to, $cc = '')
    {

        return $this->statement_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);

    }



    /**

     * When customer register, mark the contact and the customer as inactive and set the registration_confirmed field to 0

     * @param  mixed $client_id  the customer id

     * @return boolean

     */

    public function require_confirmation($client_id)
    {

        $contact_id = get_primary_contact_user_id($client_id);

        $this->db->where('userid', $client_id);

        $this->db->update(db_prefix() . 'clients', ['IsActive' => 0, 'registration_confirmed' => 0]);



        $this->db->where('id', $contact_id);

        $this->db->update(db_prefix() . 'contacts', ['active' => 0]);



        return true;

    }



    public function confirm_registration($client_id)
    {

        $contact_id = get_primary_contact_user_id($client_id);

        $this->db->where('userid', $client_id);

        $this->db->update(db_prefix() . 'clients', ['IsActive' => 1, 'registration_confirmed' => 1]);



        $this->db->where('id', $contact_id);

        $this->db->update(db_prefix() . 'contacts', ['active' => 1]);



        $contact = $this->get_contact($contact_id);



        if ($contact) {

            send_mail_template('customer_registration_confirmed', $contact);



            return true;

        }



        return false;

    }



    public function send_verification_email($id)
    {

        $contact = $this->get_contact($id);



        if (empty($contact->email)) {

            return false;

        }



        $success = send_mail_template('customer_contact_verification', $contact);



        if ($success) {

            $this->db->where('id', $id);

            $this->db->update(db_prefix() . 'contacts', ['email_verification_sent_at' => date('Y-m-d H:i:s')]);

        }



        return $success;

    }



    public function mark_email_as_verified($id)
    {

        $contact = $this->get_contact($id);



        $this->db->where('id', $id);

        $this->db->update(db_prefix() . 'contacts', [

            'email_verified_at' => date('Y-m-d H:i:s'),

            'email_verification_key' => null,

            'email_verification_sent_at' => null,

        ]);



        if ($this->db->affected_rows() > 0) {



            // Check for previous tickets opened by this email/contact and link to the contact

            $this->load->model('tickets_model');

            $this->tickets_model->transfer_email_tickets_to_contact($contact->email, $contact->id);



            return true;

        }



        return false;

    }



    public function get_clients_distinct_countries()
    {

        return $this->db->query('SELECT DISTINCT(country_id), short_name FROM ' . db_prefix() . 'clients JOIN ' . db_prefix() . 'countries ON ' . db_prefix() . 'countries.country_id=' . db_prefix() . 'clients.country')->result_array();

    }



    public function send_notification_customer_profile_file_uploaded_to_responsible_staff($contact_id, $customer_id)
    {

        $staff = $this->get_staff_members_that_can_access_customer($customer_id);

        $merge_fields = $this->app_merge_fields->format_feature('client_merge_fields', $customer_id, $contact_id);

        $notifiedUsers = [];





        foreach ($staff as $member) {

            mail_template('customer_profile_uploaded_file_to_staff', $member['email'], $member['staffid'])

                ->set_merge_fields($merge_fields)

                ->send();



            if (
                add_notification([

                    'touserid' => $member['staffid'],

                    'description' => 'not_customer_uploaded_file',

                    'link' => 'clients/client/' . $customer_id . '?group=attachments',

                ])
            ) {

                array_push($notifiedUsers, $member['staffid']);

            }

        }

        pusher_trigger_notification($notifiedUsers);

    }



    public function get_staff_members_that_can_access_customer($id)
    {

        $id = $this->db->escape_str($id);



        return $this->db->query('SELECT * FROM ' . db_prefix() . 'staff

            WHERE (

                    admin=1

                    OR staffid IN (SELECT staff_id FROM ' . db_prefix() . "customer_admins WHERE customer_id='.$id.')

                    OR staffid IN(SELECT staff_id FROM " . db_prefix() . 'staff_permissions WHERE feature = "customers" AND capability="view")

                )

            AND active=1')->result_array();

    }



    private function check_zero_columns($data)
    {

        if (!isset($data['show_primary_contact'])) {

            $data['show_primary_contact'] = 0;

        }



        if (isset($data['default_currency']) && $data['default_currency'] == '' || !isset($data['default_currency'])) {

            $data['default_currency'] = 0;

        }



        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {

            $data['country'] = 0;

        }



        if (isset($data['billing_country']) && $data['billing_country'] == '' || !isset($data['billing_country'])) {

            $data['billing_country'] = 0;

        }



        if (isset($data['shipping_country']) && $data['shipping_country'] == '' || !isset($data['shipping_country'])) {

            $data['shipping_country'] = 0;

        }



        return $data;

    }



    public function delete_contact_profile_image($id)
    {

        hooks()->do_action('before_remove_contact_profile_image');

        if (file_exists(get_upload_path_by_type('contact_profile_images') . $id)) {

            delete_dir(get_upload_path_by_type('contact_profile_images') . $id);

        }

        $this->db->where('id', $id);

        $this->db->update(db_prefix() . 'contacts', [

            'profile_image' => null,

        ]);

    }



    /**

     * @param $projectId

     * @param  string  $tasks_email

     *

     * @return array[]

     */

    public function get_contacts_for_project_notifications($projectId, $type)
    {

        $this->db->select('clientid,contact_notification,notify_contacts');

        $this->db->from(db_prefix() . 'projects');

        $this->db->where('id', $projectId);

        $project = $this->db->get()->row();



        if (!in_array($project->contact_notification, [1, 2])) {

            return [];

        }



        $this->db

            ->where('userid', $project->clientid)

            ->where('active', 1)

            ->where($type, 1);



        if ($project->contact_notification == 2) {

            $projectContacts = unserialize($project->notify_contacts);

            $this->db->where_in('id', $projectContacts);

        }



        return $this->db->get(db_prefix() . 'contacts')->result_array();

    }

    //====================== Get Staff List Type Wise ==============================

    public function GetStaffListTypeWise($StaffType)
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select(db_prefix() . 'staff.*');

        $this->db->where(db_prefix() . 'staff.SubActGroupID', $StaffType);

        $this->db->order_by('firstname,lastname', 'ASC');

        return $this->db->get(db_prefix() . 'staff')->result_array();

    }

    public function get_tds_sections()
    {
        // return $this->db->get(db_prefix() . 'tds_sections')->result_array();
        return $this->db->get(db_prefix() . 'TDSMaster')->result_array();

    }

    public function get_rootcompany()
    {



        return $this->db->get(db_prefix() . 'rootcompany')->result_array();

    }

    //============================== Get CIty List Against State COde ==============

    public function GetCityList($StateID)
    {

        $this->db->select(db_prefix() . 'xx_citylist.*');

        $this->db->where(db_prefix() . 'xx_citylist.state_id', $StateID);

        $this->db->order_by(db_prefix() . 'xx_citylist.city_name', 'ASC');

        return $this->db->get('tblxx_citylist')->result_array();

    }


    public function get_table_on_load_filter($data)
    {

        $selected_company = $this->session->userdata('root_company');

        $wh = array();





        if (isset($data['client_type']) && $data['client_type'] != '') {

            $wh[] = 'DistributorType = ' . $data['client_type'];

        }

        if (isset($data['distributor_state']) && $data['distributor_state'] != '') {

            $wh[] = 'state = ' . $data['distributor_state'];

        }

        if (isset($data['division']) && $data['division'] != '') {
            $wh[] = db_prefix() . 'clients.AccountID IN (SELECT AccountID FROM ' . db_prefix() . 'accountitemdiv WHERE ItemDivID =' . $data['division'] . ')';

        }

        if (isset($data['responsible_admin']) && $data['responsible_admin'] != '') {

            $wh[] = db_prefix() . 'clients.AccountID IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id =' . $data['division'] . ' AND company_id =' . $selected_company . ')';

        }

        if (isset($data['status']) && $data['status'] != '') {

            $wh[] = "`tblclients`.`IsActive` = '" . $data['status'] . "'";

        }

        $WHERE = '';

        if (count($wh) > 0) {

            $WHERE = implode(" AND ", $wh);

        }



        $this->db->select('tblclients.userid as userid,tblclients.AccountID as AccountID,tblclients.company,tblclients.phonenumber,tblcustomer_admins.staff_id as assigned_staff,tblclients.state,tblclients.address,tblStationMaster.StationName,tblclients.city,tblclients.IsActive as active,tblxx_citylist.city_name,(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tblcustomers_groups WHERE tblcustomers_groups.id = tblclients.DistributorType) as customerGroups');

        $this->db->join('tblcustomer_admins', 'tblclients.AccountID=tblcustomer_admins.customer_id AND tblclients.PlantID=tblcustomer_admins.company_id', 'left');

        $this->db->join('tblxx_citylist', 'tblclients.city=tblxx_citylist.id', 'left');

        $this->db->join('tblStationMaster', 'tblStationMaster.id=tblclients.StationName', 'left');

        $this->db->where('tblclients.SubActGroupID1', "100056");

        $this->db->where('tblclients.PlantID', $selected_company);

        if (isset($data['distributor_state']) && $data['distributor_state'] != '') {

            $this->db->where('state', $data['distributor_state']);

        }

        if (isset($data['client_type']) && $data['client_type'] != '') {

            $this->db->where('DistributorType', $data['client_type']);

        }

        if (isset($data['division']) && $data['division'] != '') {

            $this->db->where(db_prefix() . 'clients.AccountID IN (SELECT AccountID FROM ' . db_prefix() . 'accountitemdiv WHERE ItemDivID =' . $data['division'] . ')');



        }

        if (isset($data['responsible_admin']) && $data['responsible_admin'] != '') {



            $this->db->where(db_prefix() . 'clients.AccountID IN (SELECT customer_id FROM ' . db_prefix() . 'customer_admins WHERE staff_id =' . $data['responsible_admin'] . ' AND company_id =' . $selected_company . ')');

        }

        if (isset($data['status']) && $data['status'] != '') {

            $this->db->where(db_prefix() . 'clients.IsActive', $data['status']);

        }

        $this->db->order_by(db_prefix() . 'clients.company', 'ASC');

        $result = $this->db->get(db_prefix() . 'clients')->result_array();

        return $result;

    }

    public function Getaccountgroupssub()
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select(db_prefix() . 'accountgroupssub.*');

        $this->db->where(db_prefix() . 'accountgroupssub.SubActGroupID1', '100056');

        $this->db->order_by(db_prefix() . 'accountgroupssub.SubActGroupName', 'ASC');

        return $this->db->get('tblaccountgroupssub')->result_array();

    }

    public function get_customer()
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select([db_prefix() . 'AccountSubGroup2.*',]);

        $this->db->where(db_prefix() . 'AccountSubGroup2.IsCustomer', 'Y');

        $this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');

        return $this->db->get('tblAccountSubGroup2')->result_array();

    }

    public function get_broker()
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select([db_prefix() . 'AccountSubGroup2.*',]);

        $this->db->where(db_prefix() . 'AccountSubGroup2.IsBroker', 'Y');

        $this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');

        return $this->db->get('tblAccountSubGroup2')->result_array();

    }

    public function get_position()
    {
        // session  company id
        $selected_company = $this->session->userdata('root_company');

        // select all columns
        $this->db->select(db_prefix() . 'hr_job_position.*');
        $this->db->from(db_prefix() . 'hr_job_position');

        // WHERE condition
        // if (!empty($selected_company)) {
        //     $this->db->where(db_prefix() . 'hr_job_position.company_id', $selected_company);
        // }

        // order by
        $this->db->order_by(db_prefix() . 'hr_job_position.position_id', 'ASC');

        // execute & return result
        return $this->db->get()->result_array();
    }


    public function get_freight_terms()
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select([db_prefix() . 'FreightTerms.*',]);

        $this->db->where(db_prefix() . 'FreightTerms.IsActive', 'Y');

        $this->db->order_by(db_prefix() . 'FreightTerms.Id', 'ASC');

        return $this->db->get('tblFreightTerms')->result_array();

    }


    public function get_priority()
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select([db_prefix() . 'PriorityMaster.*',]);

        $this->db->where(db_prefix() . 'PriorityMaster.IsActive', 'Y');

        $this->db->order_by(db_prefix() . 'PriorityMaster.Id', 'ASC');

        return $this->db->get('tblPriorityMaster')->result_array();

    }

    public function get_territory()
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select([db_prefix() . 'Territory.*',]);

        $this->db->where(db_prefix() . 'Territory.IsActive', 'Y');

        $this->db->order_by(db_prefix() . 'Territory.Id', 'ASC');

        return $this->db->get('tblTerritory')->result_array();

    }

    /**

    * Get customers Route

    * @return array

*/

    public function getroute()
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select('*');

        $this->db->where(db_prefix() . 'route.PlantID', $selected_company);

        $this->db->order_by('name', 'ASC');



        return $this->db->get(db_prefix() . 'route')->result_array();

    }


    /**

    * Get All State

    * 

    * @return array

*/

    public function getallstate()
    {



        $this->db->where('country_id', '1');

        $this->db->order_by('state_name', 'ASE');

        return $this->db->get(db_prefix() . 'xx_statelist')->result_array();

    }


    /**

    * Get Item Division



    * @return array

*/



    public function get_itemDivision()
    {

        //$selected_company = $this->session->userdata('root_company');



        //$this->db->where('PlantID', $selected_company);



        return $this->db->get(db_prefix() . 'ItemsDivisionMaster')->result_array();

    }


    public function get_StationList()
    {

        $this->db->where('status', '1');



        return $this->db->get(db_prefix() . 'StationMaster')->result_array();

    }

    public function GetAllCustomerList()

		{

		 $selected_company = $this->session->userdata('root_company');

   	// $selected_company = $this->session->userdata('root_company');

		$this->db->select('tblclients.ActSubGroupID2,tblclients.ActSubGroupID1,tblclients.ActMainGroupID, tblclients.AccountID, tblclients.company, tblclients.FavouringName, tblclients.PAN, tblclients.GSTIN, tblclients.OrganisationType, tblclients.GSTType, tblclients.IsActive', FALSE);

		$this->db->from('tblclients');  // Add this line - you were missing FROM

		$this->db->join('tblAccountSubGroup2', 'tblAccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2', 'LEFT');

		$this->db->where(db_prefix() . 'AccountSubGroup2.IsCustomer', 'Y');


		$result = $this->db->get()->result_array();
		return $result;

	}



    public function get_AccountDetails($AccountID)
    {
        $this->db->select(
            db_prefix() . 'clients.*'
        );

        $this->db->from(db_prefix() . 'clients');

        $this->db->where(db_prefix() . 'clients.AccountID', $AccountID);

        return $this->db->get()->result_array();
    }



    public function getclientCompany($AccountID)
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select(db_prefix() . 'customer_admins.*');

        $this->db->where(db_prefix() . 'customer_admins.customer_id', $AccountID);

        return $this->db->get(db_prefix() . 'customer_admins')->result_array();

    }



    public function getAccoountOpnBal($AccountID)
    {

        $selected_company = $this->session->userdata('root_company');

        $FY = $this->session->userdata('finacial_year');

        $this->db->select(db_prefix() . 'accountbalances.BAL1,' . db_prefix() . 'accountbalances.PlantID,' . db_prefix() . 'accountbalances.AccountID');

        $this->db->join('tblcustomer_admins', 'tblcustomer_admins.customer_id = tblaccountbalances.AccountID AND tblcustomer_admins.company_id = tblaccountbalances.PlantID', 'LEFT');

        $this->db->where(db_prefix() . 'accountbalances.AccountID', $AccountID);

        $this->db->where(db_prefix() . 'accountbalances.FY', $FY);

        return $this->db->get(db_prefix() . 'accountbalances')->result_array();

    }



    public function getAccoountRoute($AccountID)
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select(db_prefix() . 'accountroutes.*');

        $this->db->where(db_prefix() . 'accountroutes.AccountID', $AccountID);

        $this->db->where(db_prefix() . 'accountroutes.PlantID', $selected_company);

        return $this->db->get(db_prefix() . 'accountroutes')->result_array();

    }

    public function GetAccoountRoutePoints($Routes)
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select(db_prefix() . 'RoutePoints.*,tblPointsMaster.PointName');

        $this->db->join('tblPointsMaster', 'tblPointsMaster.id = tblRoutePoints.PointID');

        $this->db->where_in(db_prefix() . 'RoutePoints.RouteID', $Routes);

        return $this->db->get(db_prefix() . 'RoutePoints')->result_array();

    }
    public function GetDistTypeState($DistType)
    {

        $this->db->select(db_prefix() . 'clients.*');

        $this->db->where_in(db_prefix() . 'clients.DistributorType', $DistType);

        return $this->db->get('tblclients')->row();

    }

    public function GetRoutePoints($routes)
    {

        $this->db->select(db_prefix() . 'RoutePoints.*,tblPointsMaster.PointName');

        $this->db->join(db_prefix() . 'PointsMaster', '' . db_prefix() . 'PointsMaster.id = ' . db_prefix() . 'RoutePoints.PointID');

        $this->db->where_in(db_prefix() . 'RoutePoints.RouteID', $routes);

        $this->db->order_by(db_prefix() . 'PointsMaster.PointName', 'ASC');

        return $this->db->get('tblRoutePoints')->result_array();

    }



    public function UpdateAccountDetails($Data)
    {
        $ShippingData = json_decode($Data['ShippingData'], true);
        $ContactData = json_decode($Data['ContactData'], true);
        unset($Data['ContactData']);
        unset($Data['ShippingData']);

        $AccountID = isset($Data["AccountID"]) ? $Data["AccountID"] : '';

        // Fix: Check if location_type exists, otherwise set default
        $location_type = isset($Data["location_type"]) ? $Data["location_type"] : '';

        $selected_company = $this->session->userdata('root_company');
        $FY = $this->session->userdata('finacial_year');
        $LogID = $this->session->userdata('username');

        // Fix: Check if route exists, otherwise set empty array
        $route = isset($Data["route"]) ? $Data["route"] : [];
        $routeArray = is_array($route) ? $route : [];
        $routeArraylen = count($routeArray);

        $CompAssign = isset($Data["CompSerializedArr"]) ? $Data["CompSerializedArr"] : '[]';
        $CompAssignArray = json_decode($CompAssign, true);
        $CompAssignArray = is_array($CompAssignArray) ? $CompAssignArray : [];
        $CompAssignArraylen = count($CompAssignArray);

        $ClientArray = array(
            'AccountName' => isset($Data["AccoountName"]) ? $Data["AccoountName"] : '',
            'FavouringName' => isset($Data["favouring_name"]) ? $Data["favouring_name"] : '',
            'Cust_group' => isset($Data["subgroup"]) ? $Data["subgroup"] : '',
            'MobileNo' => isset($Data["phonenumber"]) ? $Data["phonenumber"] : '',
            'AltMobileNo' => isset($Data["altphonenumber"]) ? $Data["altphonenumber"] : '',
            'GSTType' => isset($Data["gst_type"]) ? $Data["gst_type"] : '',
            'GSTIN' => isset($Data["vat"]) ? strtoupper($Data["vat"]) : '',
            'DistributorType' => isset($Data["groups_in"]) ? $Data["groups_in"] : '',
            'dis_per' => isset($Data["dis_per"]) ? $Data["dis_per"] : 0,
            'dis_per_taxable' => isset($Data["dis_per_taxable"]) ? $Data["dis_per_taxable"] : 0,
            'cd' => isset($Data["cd"]) ? $Data["cd"] : 0,
            'rate_print' => isset($Data["rate_print"]) ? $Data["rate_print"] : 0,
            'article' => isset($Data["article"]) ? $Data["article"] : '',
            'billing_state' => isset($Data["state"]) ? $Data["state"] : '',
            'billing_city' => isset($Data["city"]) ? $Data["city"] : '',
            'billing_address' => isset($Data["address"]) ? $Data["address"] : '',
            'Address3' => isset($Data["Address3"]) ? $Data["Address3"] : '',
            'billing_zip' => isset($Data["zip"]) ? $Data["zip"] : '',
            'CreditLimit' => isset($Data["MaxCrdAmt"]) ? $Data["MaxCrdAmt"] : 0,
            'IsActive' => isset($Data["Blockyn"]) ? $Data["Blockyn"] : 1,
            'SalesFrequency' => isset($Data["SalesFrequency"]) ? $Data["SalesFrequency"] : '',
            'StationName' => isset($Data["StationName"]) ? $Data["StationName"] : '',
            'ActSalestype' => isset($Data["ActSalestype"]) ? $Data["ActSalestype"] : '',
            'bill_till_bal' => isset($Data["bill_till_bal"]) ? $Data["bill_till_bal"] : 0,
            'GraceDay' => isset($Data["credit_days"]) ? $Data["credit_days"] : 0,
            'crate_limit' => isset($Data["crate_limit"]) ? $Data["crate_limit"] : 0,
            'FreshReturn' => isset($Data["FreshReturn"]) ? $Data["FreshReturn"] : 0,
            'DamageReturn' => isset($Data["DamageReturn"]) ? $Data["DamageReturn"] : 0,
            'latitude' => isset($Data["Latitude"]) ? $Data["Latitude"] : '',
            'longitude' => isset($Data["Longitude"]) ? $Data["Longitude"] : '',
            'StartDate' => isset($Data["StartDate"]) ? to_sql_date($Data["StartDate"]) : date('Y-m-d'),
            'shipping_state' => isset($Data["shipping_state"]) ? $Data["shipping_state"] : '',
            'shipping_city' => isset($Data["shipping_city"]) ? $Data["shipping_city"] : '',
            'shipping_street' => isset($Data["shipping_street"]) ? $Data["shipping_street"] : '',
            'shipping_zip' => isset($Data["shipping_zip"]) ? $Data["shipping_zip"] : '',
            'TAN' => isset($Data["tan_number"]) ? $Data["tan_number"] : '',
            'PriorityID' => isset($Data["priority"]) ? $Data["priority"] : '',
            'FSSAINo' => isset($Data["FLNO1"]) ? $Data["FLNO1"] : '',
            'FSSAIExpiry' => isset($Data["expiry_licence"]) ? $Data["expiry_licence"] : '',
            'TerritoryID' => isset($Data["territory"]) ? $Data["territory"] : '',
            'website' => isset($Data["website"]) ? $Data["website"] : '',
            'Attachment' => isset($Data["attachment"]) ? $Data["attachment"] : '',
            'AdditionalInfo' => isset($Data["additional_info"]) ? $Data["additional_info"] : '',
            'OrganisationType' => isset($Data["organisation_type"]) ? $Data["organisation_type"] : '',
            'PaymentTerms' => isset($Data["payment_terms"]) ? $Data["payment_terms"] : '',
            'PaymentCycle' => isset($Data["payment_cycle"]) ? $Data["payment_cycle"] : '',
            'PaymentCycleType' => isset($Data["payment_cycle_type"]) ? $Data["payment_cycle_type"] : '',
            'FreightTerms' => isset($Data["freight_term"]) ? $Data["freight_term"] : '',
            'default_currency' => isset($Data["default_currency"]) ? $Data["default_currency"] : 0,
            'IsTDS' => isset($Data["Tds"]) ? ($Data["Tds"] == 1 ? 'Y' : 'N') : 'N',
            'TDSSection' => isset($Data["Tdsselection"]) ? $Data["Tdsselection"] : '',
            'TDSPer' => isset($Data["TdsPercent"]) ? $Data["TdsPercent"] : '',
            'PAN' => isset($Data["Pan"]) ? $Data["Pan"] : '',
            'GSTIN' => isset($Data["vat"]) ? $Data["vat"] : '',
            'DeActiveReason' => isset($Data["blocked_reason"]) ? $Data["blocked_reason"] : '',
            'Lupdate' => date('Y-m-d H:i:s'),
            'UserID2' => $LogID,
            'RoutePoint' => isset($Data["route_point"]) ? $Data["route_point"] : '',
            'Trade_Type' => isset($Data["TradeType"]) ? $Data["TradeType"] : '',
        );

        $ContactsArray = array(
            'firstname' => isset($Data["firstname"]) ? $Data["firstname"] : '',
            'PositionID' => isset($Data["PositionID"]) ? $Data["PositionID"] : null,
            'lastname' => isset($Data["lastname"]) ? $Data["lastname"] : '',
            'title' => isset($Data["title"]) ? $Data["title"] : '',
            'email' => isset($Data["email"]) ? $Data["email"] : '',
            'kms' => isset($Data["kms"]) ? $Data["kms"] : '',
            'FLNO1' => isset($Data["FLNO1"]) ? $Data["FLNO1"] : '',
            'expiry_licence' => isset($Data["expiry_licence"]) ? to_sql_date($Data["expiry_licence"]) : null,
            'Pan' => isset($Data["Pan"]) ? $Data["Pan"] : '',
            'Aadhaarno' => isset($Data["Aadhaarno"]) ? $Data["Aadhaarno"] : '',
            'istcs' => isset($Data["istcs"]) ? $Data["istcs"] : 0,
            'TcsStartDate' => isset($Data["TcsStartDate1"]) ? to_sql_date($Data["TcsStartDate1"]) : null,
            'BalancesYN' => isset($Data["BalancesYN"]) ? $Data["BalancesYN"] : 0,
            'Lupdate' => date('Y-m-d H:i:s'),
            'UserID2' => $LogID
        );

        $this->db->where('AccountID', $AccountID);
        $this->db->where('PlantID', $selected_company);
        $this->db->update(db_prefix() . 'clients', $ClientArray);

        // Update Shipping Data
        if (is_array($ShippingData)) {
            foreach ($ShippingData as $index => $shipdata) {
                $shipping_id = isset($shipdata['shipping_id']) ? $shipdata['shipping_id'] : '';

                if (!empty($shipping_id)) {
                    $update_arr = [
                        'AccountID' => strtoupper($AccountID),
                        'ShippingState' => isset($shipdata['shipping_state']) ? $shipdata['shipping_state'] : '',
                        'ShippingCity' => isset($shipdata['shipping_city']) ? $shipdata['shipping_city'] : '',
                        'ShippingAdrees' => isset($shipdata['ShippingAdrees']) ? $shipdata['ShippingAdrees'] : '',
                        'ShippingPin' => isset($shipdata['ShippingPin']) ? $shipdata['ShippingPin'] : '',
                    ];
                    $this->db->where('id', $shipping_id);
                    $this->db->where('AccountID', $AccountID);
                    $this->db->update(db_prefix() . 'clientwiseshippingdata', $update_arr);
                } else {
                    $shipwisedata = array(
                        'AccountID' => strtoupper($AccountID),
                        'ShippingState' => isset($shipdata['shipping_state']) ? $shipdata['shipping_state'] : '',
                        'ShippingCity' => isset($shipdata['shipping_city']) ? $shipdata['shipping_city'] : '',
                        'ShippingAdrees' => isset($shipdata['ShippingAdrees']) ? $shipdata['ShippingAdrees'] : '',
                        'ShippingPin' => isset($shipdata['ShippingPin']) ? $shipdata['ShippingPin'] : '',
                        'UserID' => $LogID,
                        'TransDate' => date('Y-m-d H:i:s')
                    );
                    $this->db->insert(db_prefix() . 'clientwiseshippingdata', $shipwisedata);
                }
            }
        }

        // Update Contact Details
        if ($this->db->table_exists(db_prefix() . 'contactdetails')) {
            $this->db->select('id');
            $this->db->where('AccountID', $AccountID);
            $existing_contacts = $this->db->get(db_prefix() . 'contactdetails')->result_array();
            $existing_ids = array_column($existing_contacts, 'id');
            $incoming_ids = [];

            if (is_array($ContactData)) {
                foreach ($ContactData as $cdata) {
                    $c_arr = [
                        'AccountID' => strtoupper($AccountID),
                        'Name' => isset($cdata['Name']) ? $cdata['Name'] : '',
                        'Designation' => isset($cdata['Designation']) ? $cdata['Designation'] : '',
                        'Mobile' => isset($cdata['Mobile']) ? $cdata['Mobile'] : '',
                        'Phone' => isset($cdata['Phone']) ? $cdata['Phone'] : '',
                        'Email' => isset($cdata['Email']) ? $cdata['Email'] : '',
                        'SendEmail' => isset($cdata['SendEmail']) ? $cdata['SendEmail'] : 0,
                        'SendSMS' => isset($cdata['SendSMS']) ? $cdata['SendSMS'] : 0,
                    ];

                    if (isset($cdata['id']) && !empty($cdata['id'])) {
                        $incoming_ids[] = $cdata['id'];
                        $this->db->where('id', $cdata['id']);
                        $this->db->update(db_prefix() . 'contactdetails', $c_arr);
                    } else {
                        $this->db->insert(db_prefix() . 'contactdetails', $c_arr);
                    }
                }
            }

            $ids_to_delete = array_diff($existing_ids, $incoming_ids);
            if (!empty($ids_to_delete)) {
                $this->db->where_in('id', $ids_to_delete);
                $this->db->delete(db_prefix() . 'contactdetails');
            }
        }

        // Update and insert location type
        $CheckLocationRecord = $this->ChkLocationRecord($AccountID);
        if ($CheckLocationRecord) {
            if (!empty($location_type)) {
                $locType = array(
                    'LocationTypeID' => $location_type,
                    'Lupdate' => date('Y-m-d H:i:s'),
                    'UserID2' => $LogID
                );
                $this->db->where('AccountID', $AccountID);
                $this->db->where('PlantID', $selected_company);
                $this->db->update(db_prefix() . 'accountlocations', $locType);
            }
        } else {
            if (!empty($location_type)) {
                $locType = array(
                    'LocationTypeID' => $location_type,
                    'PlantID' => $selected_company,
                    'AccountID' => $AccountID
                );
                $this->db->insert(db_prefix() . 'accountlocations', $locType);
            }
        }

        // Update Contact
        $CheckContactRecord = $this->ChkContactRecord($AccountID);
        if ($CheckContactRecord) {
            $this->db->where('AccountID', $AccountID);
            $this->db->where('PlantID', $selected_company);
            $this->db->update(db_prefix() . 'contacts', $ContactsArray);
        } else {
            $ContactsArray['AccountID'] = $AccountID;
            $ContactsArray['PlantID'] = $selected_company;
            $this->db->insert(db_prefix() . 'contacts', $ContactsArray);
        }

        // Delete existing routes and insert new ones
        $this->db->where('AccountID', $AccountID);
        $this->db->where('PlantID', $selected_company);
        $this->db->delete(db_prefix() . 'accountroutes');

        // Insert Account Route
        if ($routeArraylen > 0) {
            for ($k = 0; $k < $routeArraylen; $k++) {
                if (isset($routeArray[$k]) && !empty($routeArray[$k])) {
                    $RouteID = $routeArray[$k];
                    $InsAccountRoute = array(
                        'PlantID' => $selected_company,
                        'AccountID' => strtoupper($AccountID),
                        'RouteID' => $RouteID,
                        'UserID2' => $LogID,
                        'Lupdate' => date('Y-m-d H:i:s'),
                    );
                    $this->db->insert(db_prefix() . 'accountroutes', $InsAccountRoute);
                }
            }
        }

        // Insert Company Assigned AND Opening Balance
        if ($CompAssignArraylen > 0) {
            for ($k = 0; $k < $CompAssignArraylen; $k++) {
                if (!isset($CompAssignArray[$k]))
                    continue;

                $PlantID = isset($CompAssignArray[$k][0]) ? $CompAssignArray[$k][0] : '';
                $StaffID = isset($CompAssignArray[$k][1]) ? $CompAssignArray[$k][1] : 0;
                $OBal = isset($CompAssignArray[$k][2]) ? $CompAssignArray[$k][2] : 0;
                $DrCr = isset($CompAssignArray[$k][3]) ? $CompAssignArray[$k][3] : 'DR';

                if (empty($PlantID))
                    continue;

                $CheckAdminRecord = $this->ChkAdminRecord($AccountID, $PlantID);
                if ($CheckAdminRecord) {
                    $UpdateAccountAdmin = array(
                        'staff_id' => $StaffID,
                        'UserID2' => $LogID,
                        'Lupdate' => date('Y-m-d H:i:s'),
                    );
                    $this->db->where('customer_id', $AccountID);
                    $this->db->where('company_id', $PlantID);
                    $this->db->update(db_prefix() . 'customer_admins', $UpdateAccountAdmin);
                } else {
                    $InsAccountAdmin = array(
                        'staff_id' => $StaffID,
                        'customer_id' => strtoupper($AccountID),
                        'company_id' => $PlantID,
                        'date_assigned' => date('Y-m-d H:i:s')
                    );
                    $this->db->insert(db_prefix() . 'customer_admins', $InsAccountAdmin);
                }

                if ($DrCr == "CR") {
                    $OBal = '-' . $OBal;
                }

                $CheckBalRecord = $this->ChkBalRecord($AccountID, $PlantID, $FY);
                $staff_user_id = $this->session->userdata('staff_user_id');

                if ($CheckBalRecord) {
                    if ($staff_user_id == "3") {
                        $UpdateAccountBal = array(
                            'BAL1' => $OBal,
                            'UserID2' => $LogID,
                            'Lupdate' => date('Y-m-d H:i:s'),
                        );
                        $this->db->where('AccountID', $AccountID);
                        $this->db->where('PlantID', $PlantID);
                        $this->db->where('FY', $FY);
                        $this->db->update(db_prefix() . 'accountbalances', $UpdateAccountBal);
                    }
                } else {
                    $InsAccountBal = array(
                        'PlantID' => $PlantID,
                        'AccountID' => strtoupper($AccountID),
                        'BAL1' => $OBal,
                        'FY' => $FY
                    );
                    $this->db->insert(db_prefix() . 'accountbalances', $InsAccountBal);
                }
            }
        }

        return true;
    }

    public function ChkLocationRecord($AccountID)
    {
        $selected_company = $this->session->userdata('root_company');
        $this->db->where('AccountID', $AccountID);
        $this->db->where('PlantID', $selected_company);
        return $this->db->get(db_prefix() . 'accountlocations')->row();
    }

    public function ChkContactRecord($AccountID)
    {
        $selected_company = $this->session->userdata('root_company');
        $this->db->where('AccountID', $AccountID);
        $this->db->where('PlantID', $selected_company);
        return $this->db->get(db_prefix() . 'contacts')->row();
    }

    public function ChkAdminRecord($AccountID, $PlantID)
    {
        $this->db->where('customer_id', $AccountID);
        $this->db->where('company_id', $PlantID);
        return $this->db->get(db_prefix() . 'customer_admins')->row();
    }

    public function GetPartyList()
    {
        $this->db->select('AccountID,company');
        $this->db->where('IsActive', 1);
        $this->db->order_by('company', 'ASC');
        return $this->db->get(db_prefix() . 'clients')->result_array();


    }
    //=============================== Get Vehicle List =============================

    public function getvehicle()
    {

        $selected_company = $this->session->userdata('root_company');

        $this->db->select(db_prefix() . 'vehicle.*');

        $this->db->where(db_prefix() . 'vehicle.PlantID', $selected_company);

        $this->db->order_by('VehicleID', 'ASC');

        return $this->db->get(db_prefix() . 'vehicle')->result_array();

    }

    /**
     * Convert date format from DD/MM/YYYY to YYYY-MM-DD
     * @param  string $dateStr Date string in DD/MM/YYYY format
     * @return string Date string in YYYY-MM-DD format or null if invalid
     */
    private function convert_date_format($dateStr)
    {
        if (empty($dateStr)) {
            log_message('debug', 'convert_date_format: Empty date string');
            return null;
        }

        log_message('debug', 'convert_date_format INPUT: ' . $dateStr);

        // Try parsing with different separators
        $date_parts = [];
        if (strpos($dateStr, '/') !== false) {
            $date_parts = explode('/', $dateStr);
        } elseif (strpos($dateStr, '-') !== false) {
            $date_parts = explode('-', $dateStr);
        } else {
            $date_parts = [$dateStr];
        }

        if (count($date_parts) !== 3) {
            log_message('debug', 'convert_date_format: Invalid format - got ' . count($date_parts) . ' parts');
            return $dateStr;
        }

        $day = trim($date_parts[0]);
        $month = trim($date_parts[1]);
        $year = trim($date_parts[2]);

        // Ensure proper formatting with leading zeros
        $formatted = sprintf('%04d-%02d-%02d', intval($year), intval($month), intval($day));

        log_message('debug', 'convert_date_format OUTPUT: ' . $formatted);

        // Basic validation - check if day and month are reasonable
        $day_int = intval($day);
        $month_int = intval($month);
        $year_int = intval($year);

        if ($month_int < 1 || $month_int > 12 || $day_int < 1 || $day_int > 31) {
            log_message('debug', 'convert_date_format: Invalid day/month values - Day: ' . $day_int . ', Month: ' . $month_int);
            return null;
        }

        return $formatted;
    }

    /**
     * Fetch shipping/location data from tblclientwiseshippingdata by AccountID
     * @param  string $AccountID Client Account ID
     * @return array Shipping data rows
     */
    public function getShippingDataByAccountID($AccountID)
    {
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblclientwiseshippingdata')->result_array();
    }

    /**
     * Fetch bank details from tblBankMaster by AccountID
     * @param  string $AccountID Client Account ID
     * @return array Bank details rows
     */
    public function getBankDetailsByAccountID($AccountID)
    {
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblBankMaster')->result_array();
    }

    /**
     * Fetch contact details from tblcontacts by AccountID
     * @param  string $AccountID Client Account ID
     * @return array Contact details rows
     */
    public function getContactDetailsbyAccountID($AccountID)
    {
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblcontacts')->result_array();
    }
      public function getBrokerDetailsbyAccountID($AccountID)
    {
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblPartyBrokerMaster')->result_array();
    }

    /**
     * Get comprehensive account details by AccountID
     * Fetches data from tblclients, tblclientwiseshippingdata, tblBankMaster, and tblcontacts
     * @param  string $AccountID Client Account ID
     * @return array Comprehensive account data
     */
    public function getComprehensiveAccountDataByID($AccountID)
    {
        $clientDetails = $this->get_AccountDetails($AccountID);
        $shippingData = $this->getShippingDataByAccountID($AccountID);
        $bankData = $this->getBankDetailsByAccountID($AccountID);
        $contactData = $this->getContactDetailsbyAccountID($AccountID);
        $broker = $this->getBrokerDetailsbyAccountID($AccountID);

        return array(
            'clientDetails' => !empty($clientDetails) ? $clientDetails[0] : array(),
            'shippingData' => $shippingData,
            'bankData' => $bankData,
            'contactData' => $contactData,
            'broker' => $broker
        );
    }

    /**
     * Check if GSTIN already exists in database
     * @param  string $gstin GSTIN number
     * @param  int $exclude_userid User ID to exclude (for update operations)
     * @return array|null Existing client record if found, null otherwise
     */
    public function check_gstin_exists($gstin, $exclude_userid = 0)
    {
        $this->db->where('GSTIN', strtoupper($gstin));

        if ($exclude_userid > 0) {
            $this->db->where('userid !=', $exclude_userid);
        }

        $this->db->limit(1);
        $result = $this->db->get('tblclients')->row_array();

        return $result;
    }


	public function GetNextCustomerCode($ActSubGroupID2)
	
	{

		// Count existing vendors with this ActSubGroupID2
		$this->db->select('COUNT(userid) as vendor_count');
		$this->db->from('tblclients');
		$this->db->where('ActSubGroupID2', $ActSubGroupID2);
		$count_result = $this->db->get()->row();
		
		$vendor_count = $count_result ? intval($count_result->vendor_count) : 0;
		$next_number = $vendor_count + 1;
		
		// Get category details (code and name)
		$this->db->select('SubActGroupID, SubActGroupName');
		$this->db->from('tblAccountSubGroup2');
		$this->db->where('SubActGroupID', $ActSubGroupID2);
		$category = $this->db->get()->row();

	    // Get category details (code and name)
		$this->db->select('ShortCode');
		$this->db->from('tblAccountSubGroup2');
		$this->db->where('SubActGroupID', $ActSubGroupID2);
		$ShortCode = $this->db->get()->row();
		
		$category_code = $category ? $category->SubActGroupID : $ActSubGroupID2;
		$category_name = $category ? $category->SubActGroupName : '';
		$short_code = $ShortCode ? $ShortCode->ShortCode : '';
		
		// Format vendor code: 'V' prefix + next_number (padded with zeros)
		// Example: count=1 -> V00002 (count + 1)
		$vendor_code = $short_code . sprintf('%05d', $next_number);
		
		return [
			'next_code' => $vendor_code,
			'count' => $vendor_count,
			'category_code' => $category_code,
			'category_name' => $category_name,
			'short_code' => $short_code
		];
	}

}