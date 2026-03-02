<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Broker_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function get_broker()
    {
        $this->db->select([db_prefix() . 'AccountSubGroup2.*',]);
        $this->db->where(db_prefix() . 'AccountSubGroup2.IsBroker', 'Y');
        $this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');
        return $this->db->get('tblAccountSubGroup2')->result_array();
    }

    public function GetNextBrokerCode($ActSubGroupID2 = null)
    {
        $this->db->select('COUNT(AccountID) as broker_count');
        $this->db->from('tblclients');
        $this->db->join(
            db_prefix() . 'AccountSubGroup2',
            db_prefix() . 'AccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2',
            'left'
        );
        $this->db->where(db_prefix() . 'AccountSubGroup2.IsBroker', 'Y');

        // Optional — only count broker-type records if needed
        // Uncomment if tblclients contains mixed account types
        // $this->db->where('IsBroker', 'Y');

        $count_result = $this->db->get()->row();

        $broker_count = $count_result ? intval($count_result->broker_count) : 0;

        $next_number = $broker_count + 1;

        // Generate global broker code
        $short_code = 'B';
        $broker_code = $short_code . sprintf('%05d', $next_number);

        // Category info is optional now
        $broker_name = '';

        if ($ActSubGroupID2) {
            $this->db->select('SubActGroupName');
            $this->db->from('tblAccountSubGroup2');
            $this->db->where('SubActGroupID', $ActSubGroupID2);
            $category = $this->db->get()->row();

            $broker_name = $category ? $category->SubActGroupName : '';
        }

        return [
            'next_code'  => $broker_code,
            'count'      => $broker_count,
            'broker_code' => $broker_code,
            'broker_name' => $broker_name,
            'short_code' => $short_code
        ];
    }


    public function get_vendor()
    {
        $this->db->select('tblclients.*');
        $this->db->from('tblclients');
        $this->db->join(
            db_prefix() . 'AccountSubGroup2',
            db_prefix() . 'AccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2',
            'left'
        );
        $this->db->where(db_prefix() . 'AccountSubGroup2.IsVendor', 'Y');
        $this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_customer()
    {
        $this->db->select('tblclients.*');
        $this->db->from('tblclients');
        $this->db->join(
            db_prefix() . 'AccountSubGroup2',
            db_prefix() . 'AccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2',
            'left'
        );
        $this->db->where(db_prefix() . 'AccountSubGroup2.IsCustomer', 'Y');
        $this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');

        return $this->db->get()->result_array();
    }


    public function GetAllBrokerList()
    {
        $this->db->select('tblclients.ActSubGroupID2,tblclients.ActSubGroupID1,tblclients.ActMainGroupID, tblclients.AccountID, tblclients.company, tblclients.FavouringName, tblclients.PAN, tblclients.GSTIN, tblclients.OrganisationType, tblclients.GSTType, tblclients.IsActive', FALSE);

        $this->db->from('tblclients');  // Add this line - you were missing FROM

        $this->db->join('tblAccountSubGroup2', 'tblAccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2', 'LEFT');

        $this->db->where(db_prefix() . 'AccountSubGroup2.IsBroker', 'Y');

        $this->db->order_by('tblclients.AccountID', 'ASC');

        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_position()
    {

        // select all columns
        $this->db->select(db_prefix() . 'hr_job_position.*');
        $this->db->from(db_prefix() . 'hr_job_position');

        // order by
        $this->db->order_by(db_prefix() . 'hr_job_position.position_id', 'ASC');

        // execute & return result
        return $this->db->get()->result_array();
    }

    public function getallcountry()
    {
        return $this->db->get(db_prefix() . 'countries')->result_array();
    }



    public function add_to_tblclients($form_data, $userid = 0)
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
                    log_message('debug', 'SaveBroker: ActGroupID fetched for ActSubGroupID1 ' . $insert_data['ActSubGroupID1'] . ' = ' . $group_result->ActGroupID);
                }
            } else {
                log_message('debug', 'SaveBroker: ActGroupID/ActSubGroupID1 not found for groups_in ' . $form_data['groups_in']);
            }
        }


        // mapping of possible incoming fields -> tblclients columns
        $mapping = [
            'AccountID' => isset($form_data['AccountID']) ? $form_data['AccountID'] : null,
            'PlantID' => $plant,
            'userid' => $user,
            'company' => isset($form_data['AccountName']) ? $form_data['AccountName'] : null,
            'FavouringName' => isset($form_data['FavouringName']) ? $form_data['FavouringName'] : null,
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

            // Billing Information fields
            'OrganisationType' => isset($form_data['organisation_type']) ? $form_data['organisation_type'] : null,
            'GSTType' => isset($form_data['gsttype']) ? $form_data['gsttype'] : (isset($form_data['gst_type']) ? $form_data['gst_type'] : null),

            // Other Information fields
            'Attachment' => isset($form_data['attachment']) ? $form_data['attachment'] : (isset($form_data['Attachment']) ? $form_data['Attachment'] : null),
            'AdditionalInfo' => isset($form_data['additional_info']) ? $form_data['additional_info'] : null,


            // Group/Customer Category
            'ActSubGroupID2' => isset($form_data['groups_in']) ? $form_data['groups_in'] : (isset($form_data['ActSubGroupID2']) ? $form_data['ActSubGroupID2'] : null),
            'ActSubGroupID1' => isset($insert_data['ActSubGroupID1']) ? $insert_data['ActSubGroupID1'] : null,
            'ActMainGroupID' => isset($insert_data['ActGroupID']) ? $insert_data['ActGroupID'] : null,

            // Active/Blocked Status
            'IsActive' => isset($form_data['IsActive']) ? ($form_data['IsActive'] == 'Y' ? 'Y' : 'N') : null,
            'DeActiveReason' => isset($form_data['blocked_reason']) ? $form_data['blocked_reason'] : null,

            'CreatedBy' => is_staff_logged_in() ? get_staff_user_id() : 0,
            'TransDate' => date('Y-m-d H:i:s'),
            // 'UserID2' => $user,
            // 'Lupdate' => date('Y-m-d H:i:s'),
        ];

        // Build final insert data (only include non-null values)
        $insert_data = [];
        foreach ($mapping as $col => $val) {
            if ($val !== null && $val !== '') {
                $insert_data[$col] = $val;
            }
        }


        // Debug: Log what we're about to insert
        log_message('debug', 'add_to_tblclients insert_data: ' . json_encode($insert_data));
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


                $vendor   = isset($form_data['vendor']) ? $form_data['vendor'] : null;
                $customer = isset($form_data['customer']) ? $form_data['customer'] : null;

                if (!empty($vendor)) {
                    $this->insert_party_broker_master(
                        $vendor,
                        $insert_data['AccountID'],
                        $plant,
                        $user,
                        false
                    );
                }

                if (!empty($customer)) {
                    $this->insert_party_broker_master(
                        $customer,
                        $insert_data['AccountID'],
                        $plant,
                        $user,
                        false
                    );
                }



                // // Handle Vendor master data insertion into tblPartyVendorMaster
                // if (isset($form_data['vendor']) && !empty($form_data['vendor'])) {
                //     $this->insert_party_broker_master($insert_data['AccountID'], $form_data['broker'], $plant, $user, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
                // }

                // // Handle Customer master data insertion into tblPartyCustomerMaster
                // if (isset($form_data['customer']) && !empty($form_data['customer'])) {
                //     $this->insert_party_broker_master($insert_data['AccountID'], $form_data['customer'], $plant, $user, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
                // }

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

    private function insert_party_broker_master(
        $V_C_account_id,
        $account_id,
        $plant_id = null,
        $user_id = null,
        $is_update = false
    ) {
        try {

            if (empty($account_id)) {
                log_message('error', 'insert_party_broker_master - Missing AccountID');
                return;
            }

            // ===============================
            // Get PlantID from session
            // ===============================
            if (empty($plant_id)) {
                $plant_id = $this->session->userdata('root_company');
            }

            // ===============================
            // Get UserID from session
            // ===============================
            if (empty($user_id)) {
                $user_id = $this->session->userdata('staff_user_id');
            }

            $current_date = date('Y-m-d H:i:s');
            // ===============================
            // Get Minimum Contact ID
            // ===============================
            $this->db->select_min('id'); // change 'id' if PK name is different
            $this->db->where('AccountID', $account_id);
            $contact_row = $this->db->get('tblcontacts')->row();

            $broker_contact_id = null;

            if ($contact_row && !empty($contact_row->id)) {
                $broker_contact_id = $contact_row->id;
            }

            // ===============================
            // Prepare Insert Data
            // ===============================
            $insert_data = [
                'PlantID'         => $plant_id,
                'AccountID'       => $V_C_account_id,   // Vendor/Customer ID
                'BrokerID'        => $account_id,   // Same as AccountID
                'BrokerContactID' => $broker_contact_id,
                'UserID'          => $user_id,
                'UserID2'         => $user_id,
                'TransDate'       => $current_date,
                'Lupdate'         => $current_date,
                // 'IsActive'        => 'Y',
                // 'CreatedBy'       => $created_by
            ];

            // Remove null values
            $insert_data = array_filter($insert_data, function ($v) {
                return $v !== null && $v !== '';
            });
           

            // log_message('debug', 'tblPartyBrokerMaster insert: ' . json_encode($insert_data));

            $this->db->insert('tblPartyBrokerMaster', $insert_data);

            if ($this->db->affected_rows() <= 0) {
                log_message('error', 'Insert failed in tblPartyBrokerMaster: ' . $this->db->error()['message']);
            }
        } catch (Exception $e) {
            log_message('error', 'Error in insert_party_broker_master: ' . $e->getMessage());
        }
    }



    public function update_tblclients($form_data, $userid = 0)
    {

    $account_id = isset($form_data['AccountID']) 
    ? $form_data['AccountID'] 
    : null;

if (empty($account_id)) {
    log_message('error', 'update_tblclients: AccountID missing in form_data');
    return false;
}




        // Map form fields to database column names - only non-null values

        $data = [

            // 'AccountID' => isset($form_data['AccountID']) ? $form_data['AccountID'] : null,

            'company' => isset($form_data['AccountName']) ? $form_data['AccountName'] : null,

            'FavouringName' => isset($form_data['FavouringName']) ? $form_data['FavouringName'] : null,

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

            'IsActive' => isset($form_data['IsActive']) ? ($form_data['IsActive'] == 'Y' ? 'Y' : 'N') : null,

            'ActSubGroupID2' => isset($form_data['groups_in']) ? $form_data['groups_in'] : null,

            'DeActiveReason' => isset($form_data['blocked_reason']) ? $form_data['blocked_reason'] : null,
            'Attachment' => isset($form_data['attachment']) ? $form_data['attachment'] : (isset($form_data['Attachment']) ? $form_data['Attachment'] : null),

            'AdditionalInfo' => isset($form_data['additional_info']) ? $form_data['additional_info'] : null,


            // 'Attachment' => isset($form_data['attachment']) ? $form_data['attachment'] : (isset($form_data['Attachment']) ? $form_data['Attachment'] : null),


            'Lupdate' => date('Y-m-d H:i:s'),

        ];



        // Filter out null and empty-string values to avoid unintentionally
        // overwriting existing DB values with blank values when form fields
        // are submitted empty. Preserve numeric 0 and explicit '0' values.
        $update_data = array_filter($data, function ($value) {
            if (is_null($value)) {
                return false;
            }
            if (is_string($value) && trim($value) === '') {
                return false;
            }
            return true;
        });

        try {
            $this->db->trans_start();


            $this->db->where('AccountID', $account_id);

            $this->db->update('tblclients', $update_data);


            // Handle contact data update into tblcontacts - SAFE UPDATE (only if ContactData provided)
            // This prevents accidental deletion of contacts when form doesn't send contact data
            if (isset($form_data['ContactData']) && $form_data['ContactData'] !== '' && isset($account_id
)) {
                $plant_id = isset($form_data['PlantID']) ? $form_data['PlantID'] : $this->session->userdata('root_company');
                // Use UPDATE mode (true) - will smart update existing and insert new
                $this->insert_contacts_into_tblcontacts($form_data['ContactData'], $account_id
, $plant_id, $userid, true);
            }

            // Handle bank data update into tblBankMaster - SAFE UPDATE (only if is_bank_detail is explicitly set)
            if (isset($form_data['is_bank_detail']) && $form_data['is_bank_detail'] == '1' && isset($account_id
)) {
                // Update bank data (UPDATE mode - will smart update existing bank records)
                $this->insert_or_update_tblBankMaster($account_id
, $form_data, $userid, $userid, true);
            } elseif (isset($form_data['is_bank_detail']) && $form_data['is_bank_detail'] == '0' && isset($account_id
)) {
                // If is_bank_detail is explicitly 0, optionally delete bank records
                // Uncomment the next line only if you want to delete bank records when user says "No bank details"
                // $this->db->where('AccountID', $form_data['AccountID']);
                // $this->db->delete('tblBankMaster');
            }

            // ===============================
            // Handle Party Broker Master Update
            // ===============================
            if (isset($account_id
)) {
                $account_id = $account_id
;

                $plant_id   = isset($form_data['PlantID']) ? $form_data['PlantID'] : $this->session->userdata('root_company');
                $user_id    = !empty($userid) ? $userid : $this->session->userdata('staff_user_id');


                $vendor   = isset($form_data['vendor']) ? $form_data['vendor'] : null;
                $customer = isset($form_data['customer']) ? $form_data['customer'] : null;


// Delete ALL old relations for this broker
$this->db->where('BrokerID', $account_id);
$this->db->delete('tblPartyBrokerMaster');

                // If Vendor selected
                if (!empty($vendor)) {
                    $this->insert_party_broker_master(
                        $vendor,
                       $account_id
,
                        $plant_id,
                        $user_id,
                        true
                    );
                }

                // If Customer selected
                if (!empty($customer)) {
                    $this->insert_party_broker_master(
                        $customer,
                       $account_id
,
                        $plant_id,
                        $user_id,
                        true
                    );
                }
            }
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed: ' . json_encode($this->db->error()));
                return false;
            }

            return true;
        } catch (Exception $e) {

            // Log the error for debugging

            log_message('error', 'tblclients update error: ' . $e->getMessage());

            return false;
        }
        return false;
    }


    public function getComprehensiveAccountDataByID($AccountID)
    {
        $clientDetails = $this->get_AccountDetails($AccountID);
        $bankData = $this->getBankDetailsByAccountID($AccountID);
        $contactData = $this->getContactDetailsbyAccountID($AccountID);
        $brokerData = $this->getBrokerDetailsbyAccountID($AccountID);



        return array(
            'clientDetails' => !empty($clientDetails) ? $clientDetails[0] : array(),
            'bankData' => $bankData,
            'contactData' => $contactData,
            'brokerData' => $brokerData
        );
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

    public function getBankDetailsByAccountID($AccountID)
    {
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblBankMaster')->result_array();
    }

    public function getContactDetailsbyAccountID($AccountID)
    {
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblcontacts')->result_array();
    }
    public function getBrokerDetailsbyAccountID($AccountID)
    {
        $this->db->where('BrokerID', $AccountID);
        return $this->db->get('tblPartyBrokerMaster')->result_array();
    }
}
