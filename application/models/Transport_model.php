<?php

use app\services\utilities\Arr;

defined('BASEPATH') or exit('No direct script access allowed');

class transport_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getallcountry()
    {
        return $this->db->get(db_prefix() . 'countries')->result_array();
    }

    public function get_transporter()
    {
        // $sql = 'SELECT ' . db_prefix() . 'AccountSubGroup2.SubActGroupID,' . db_prefix() . 'AccountSubGroup2.ShortCode,' . db_prefix() . 'AccountSubGroup2.SubActGroupName
        // 	FROM ' . db_prefix() . 'AccountSubGroup2
        // 	WHERE IsTransporter = "Y"';
        // $result = $this->db->query($sql)->result_array();
        // return $result;

        $this->db->select([db_prefix() . 'AccountSubGroup2.*',]);

        $this->db->where(db_prefix() . 'AccountSubGroup2.IsTransporter', 'Y');

        $this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');

        return $this->db->get('tblAccountSubGroup2')->result_array();
    }

    public function get_Count($groups_in)
    {
        $sql = 'SELECT count(' . db_prefix() . 'clients.ActSubGroupID2) as count
            FROM ' . db_prefix() . 'clients
			WHERE ActSubGroupID2 = ' . $groups_in;
        return $this->db->query($sql)->result_array();
    }

    public function GetCityList($id)
    {
        $query = $this->db->get_where('tblxx_citylist', array('state_id' => $id));
    }

    public function GetAllTransporterList()
    {
        $this->db->select('tblclients.ActSubGroupID2,tblclients.ActSubGroupID1,tblclients.ActMainGroupID, tblclients.AccountID, tblclients.company, tblclients.FavouringName, tblclients.PAN, tblclients.GSTIN, tblclients.OrganisationType, tblclients.GSTType, tblclients.IsActive', FALSE);
        $this->db->from('tblclients');  // Add this line - you were missing FROM

        $this->db->join('tblAccountSubGroup2', 'tblAccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2', 'LEFT');

        $this->db->where(db_prefix() . 'AccountSubGroup2.IsTransporter', 'Y');
        $this->db->order_by('tblclients.AccountID', 'ASC');

        $result = $this->db->get()->result_array();
        return $result;
    }

    // Get comprehensive account data by AccountID
    public function getComprehensiveAccountDataByID($AccountID)
    {
        $clientDetails = $this->get_AccountDetails($AccountID);
        $bankData = $this->getBankDetailsByAccountID($AccountID);
        $contactData = $this->getContactDetailsbyAccountID($AccountID);
        $stateData = $this->get_transport_states($AccountID);
        $attachments  = $this->get_transport_attachments($AccountID);


        return array(
            'clientDetails' => !empty($clientDetails) ? $clientDetails[0] : array(),
            'bankData' => $bankData,
            'contactData' => $contactData,
            'stateData' => $stateData,
            'attachments' => $attachments
        );
    }

    // Account details by AccountID
    public function get_AccountDetails($AccountID)
    {
        $this->db->select(db_prefix() . 'clients.*');
        $this->db->from(db_prefix() . 'clients');
        $this->db->where(db_prefix() . 'clients.AccountID', $AccountID);

        return $this->db->get()->result_array();
    }

    // Bank details by AccountID
    public function getBankDetailsByAccountID($AccountID)
    {
        $this->db->select(db_prefix() . 'BankMaster.*');
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblBankMaster')->result_array();
    }

    // Contact details by AccountID
    public function getContactDetailsbyAccountID($AccountID)
    {
        $this->db->where('AccountID', $AccountID);
        $contacts = $this->db->get('tblcontacts')->result_array();

        // Map database field names to frontend field names
        $mappedContacts = array();
        foreach ($contacts as $contact) {
            $mappedContacts[] = array(
                'id' => $contact['id'],
                'Name' => $contact['firstname'],
                'Designation' => $contact['PositionID'],
                'Mobile' => $contact['phonenumber'],
                'Email' => $contact['email'],
                'SendSMS' => $contact['IsSmsYN'] == 'Y' ? 1 : 0,
                'SendEmail' => $contact['IsEmailYN'] == 'Y' ? 1 : 0,
                'PositionID' => $contact['PositionID'],
                // Keep original fields for reference
                'firstname' => $contact['firstname'],
                'phonenumber' => $contact['phonenumber'],
                'IsEmailYN' => $contact['IsEmailYN'],
                'IsSmsYN' => $contact['IsSmsYN']
            );
        }

        return $mappedContacts;
    }

    // Transport states by AccountID
    public function get_transport_states($AccountID)
    {
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblTransportState')->result_array();
    }

    // Transport attachments by AccountID
    public function get_transport_attachments($AccountID)
    {
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblTransportAttach')->result_array();
    }
    // End of Get comprehensive account data by AccountID

    public function add_to_tblclients($form_data, $userid = 0)
    {
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
                    log_message('debug', 'SaveTransport: ActGroupID fetched for ActSubGroupID1 ' . $insert_data['ActSubGroupID1'] . ' = ' . $group_result->ActGroupID);
                }
            } else {
                log_message('debug', 'SaveTransport: ActGroupID/ActSubGroupID1 not found for groups_in ' . $form_data['groups_in']);
            }
        }


        // mapping of possible incoming fields -> tblclients columns
        $mapping = [
            'AccountID' => isset($form_data['AccountID']) ? $form_data['AccountID'] : null,
            'PlantID' => $plant,
            'userid' => $user,
            'company' => isset($form_data['AccoountName']) ? $form_data['AccoountName'] : null,
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


            // Other Information fields
            // 'Attachment' => isset($form_data['attachment']) ? $form_data['attachment'] : (isset($form_data['Attachment']) ? $form_data['Attachment'] : null),,

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

                // Handle transport states: accept checkbox array `person_check` or single `state` value
                if (isset($form_data['person_check']) && !empty($form_data['person_check'])) {
                    $states_for_insert = $form_data['person_check'];
                } elseif (isset($form_data['state']) && $form_data['state'] !== '') {
                    $states_for_insert = array($form_data['state']);
                } else {
                    $states_for_insert = array();
                }

                if (!empty($states_for_insert)) {
                    $this->insert_or_update_transport_states($insert_data['AccountID'], $states_for_insert, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
                }

                log_message('error', 'FILES: ' . print_r($_FILES, true));
                // Handle file attachments - accept filenames or uploaded file arrays ($_FILES)
                $attachment_keys = ['PANCard', 'Aadhar', 'Permit', 'Photo', 'GST', 'ShopAct', 'Cheque', 'AddressProof'];
                $has_attachment = false;
                foreach ($attachment_keys as $k) {
                    if ((isset($form_data[$k]) && $form_data[$k] !== '' && $form_data[$k] !== '(binary)') || isset($_FILES[$k]) || (isset($form_data[$k]) && is_array($form_data[$k]) && !empty($form_data[$k]['tmp_name']))) {
                        $has_attachment = true;
                        break;
                    }
                }
                if ($has_attachment) {
                    $this->insert_or_update_transport_attachments(
                        $insert_data['AccountID'],
                        $user,
                        $insert_data['CreatedBy'],
                        false
                    );
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
     * Insert or update transport attachments into tblTransportAttach
     * Expects $attachment_data to contain file names for known keys (e.g. 'PANCard' => 'file.pdf')
     */
    private function insert_or_update_transport_attachments(
        $account_id,
        $user_id,
        $created_by
    ) {
        try {

            $uploadDirRel = 'uploads/Transporter/';
            $uploadDir = FCPATH . $uploadDirRel;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fields = [
                'PANCard'      => 'PAN Card',
                'Aadhar'       => 'Aadhar Card',
                'Permit'       => 'Transport Permit',
                'Photo'        => 'Owner Photograph',
                'GST'          => 'GST Certificate',
                'ShopAct'      => 'Shop Act',
                'Cheque'       => 'Cancel Cheque',
                'AddressProof' => 'Address Proof',
            ];

            $now = date('Y-m-d H:i:s');

            foreach ($fields as $key => $label) {

    // skip if no upload
    if (
        !isset($_FILES[$key]) ||
        $_FILES[$key]['error'] !== UPLOAD_ERR_OK ||
        empty($_FILES[$key]['name'])
    ) {
        continue;
    }

    $file = $_FILES[$key];

    /* =========================
       CHECK EXISTING RECORD
    ========================= */

    $existing = $this->db
        ->where('AccountID', $account_id)
        ->where('name', $label)
        ->get('tblTransportAttach')
        ->row();

    /* =========================
       DELETE OLD PHYSICAL FILE
    ========================= */

    if ($existing && file_exists(FCPATH . $existing->file)) {
        unlink(FCPATH . $existing->file);
    }

    /* =========================
       UPLOAD NEW FILE
    ========================= */

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    $safe = preg_replace(
        '/[^a-zA-Z0-9-_]/',
        '_',
        pathinfo($file['name'], PATHINFO_FILENAME)
    );

    $newName = $safe . '_' . uniqid() . '.' . $ext;
    $target = $uploadDir . $newName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        log_message('error', "Upload failed → $label");
        continue;
    }

    $data = [
        'file'    => $uploadDirRel . $newName,
        'lupdate' => $now,
        'UserID'  => $user_id,
        'UserID2' => $created_by,
    ];

    /* =========================
       UPDATE OR INSERT
    ========================= */

    if ($existing) {

        // ✅ UPDATE existing row
        $this->db
            ->where('id', $existing->id)
            ->update('tblTransportAttach', $data);

        log_message('debug', "Attachment UPDATED → $label");

    } else {

        // ✅ INSERT new record
        $data['AccountID'] = $account_id;
        $data['name']      = $label;
        $data['TransDate'] = $now;

        $this->db->insert('tblTransportAttach', $data);

        log_message('debug', "Attachment INSERTED → $label");
    }
}

        } catch (Exception $e) {

            log_message('error', 'Attachment handler crashed → ' . $e->getMessage());
        }
    }





    /**
     * Insert or update transport states into tblTransportState
     * $states expected as array of state short codes (e.g. ['MH','DL'])
     */
    private function insert_or_update_transport_states($account_id, $states, $user_id, $is_update = false)
    {
        try {
            if (!is_array($states)) {
                // If states are sent as JSON string, try decode
                $decoded = json_decode($states, true);
                if (is_array($decoded)) {
                    $states = $decoded;
                } else {
                    // Not an array - nothing to do
                    log_message('debug', 'insert_or_update_transport_states: states not array for AccountID: ' . $account_id);
                    return;
                }
            }

            if ($is_update === true) {
                $this->db->where('AccountID', $account_id);
                $this->db->delete('tblTransportState');
                log_message('debug', 'Deleted existing transport states for AccountID: ' . $account_id);
            }

            $now = date('Y-m-d H:i:s');
            $inserted = 0;
            foreach ($states as $st) {
                $st = trim($st);
                if ($st === '') continue;
                $row = [
                    'AccountID' => $account_id,
                    'State' => $st,
                    'UserID' => $user_id,
                    'TransDate' => $now,
                ];

                $this->db->insert('tblTransportState', $row);
                if ($this->db->affected_rows() > 0) {
                    $inserted++;
                } else {
                    log_message('error', 'Failed to insert transport state for AccountID: ' . $account_id . ' - State: ' . $st . ' - DB Error: ' . json_encode($this->db->error()));
                }
            }
            log_message('debug', 'Total transport states inserted for AccountID ' . $account_id . ': ' . $inserted);
        } catch (Exception $e) {
            log_message('error', 'Error inserting/updating transport states: ' . $e->getMessage());
        }
    }


    public function update_tblclients($form_data, $userid = 0)
    {

        // Map form fields to database column names - only non-null values

        $data = [

            'company' => isset($form_data['AccoountName']) ? $form_data['AccoountName'] : null,

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

            'IsActive' => isset($form_data['IsActive']) ? ($form_data['IsActive'] == 'Y' ? 'Y' : 'N') : null,

            'ActSubGroupID2' => isset($form_data['groups_in']) ? $form_data['groups_in'] : null,

            'DeActiveReason' => isset($form_data['blocked_reason']) ? $form_data['blocked_reason'] : null,

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

            $this->db->where('AccountID', $form_data['AccountID']);

            $this->db->update('tblclients', $update_data);


            // Handle contact data update into tblcontacts - SAFE UPDATE (only if ContactData provided)
            // This prevents accidental deletion of contacts when form doesn't send contact data
            if (isset($form_data['ContactData']) && $form_data['ContactData'] !== '' && isset($form_data['AccountID'])) {
                $plant_id = isset($form_data['PlantID']) ? $form_data['PlantID'] : $this->session->userdata('root_company');
                // Use UPDATE mode (true) - will smart update existing and insert new
                $this->insert_contacts_into_tblcontacts($form_data['ContactData'], $form_data['AccountID'], $plant_id, $userid, true);
            }

            // Handle bank data update into tblBankMaster - SAFE UPDATE (only if is_bank_detail is explicitly set)
            if (isset($form_data['is_bank_detail']) && $form_data['is_bank_detail'] == '1' && isset($form_data['AccountID'])) {
                // Update bank data (UPDATE mode - will smart update existing bank records)
                $this->insert_or_update_tblBankMaster($form_data['AccountID'], $form_data, $userid, $userid, true);
            } elseif (isset($form_data['is_bank_detail']) && $form_data['is_bank_detail'] == '0' && isset($form_data['AccountID'])) {
                // If is_bank_detail is explicitly 0, optionally delete bank records
                // Uncomment the next line only if you want to delete bank records when user says "No bank details"
                // $this->db->where('AccountID', $form_data['AccountID']);
                // $this->db->delete('tblBankMaster');
            }

            // Update transport states if provided (SAFE UPDATE)
            if (isset($form_data['person_check']) && isset($form_data['AccountID'])) {
                $this->insert_or_update_transport_states($form_data['AccountID'], $form_data['person_check'], $userid, true);
            }

            // Update attachments if any provided - treat as update mode (replace existing)
            $attachment_keys = ['PANCard', 'Aadhar', 'Permit', 'Photo', 'GST', 'ShopAct', 'Cheque', 'AddressProof'];
            $has_attachment = false;
            foreach ($attachment_keys as $k) {
                if (
                    isset($_FILES[$k]) &&
                    $_FILES[$k]['error'] === UPLOAD_ERR_OK &&
                    !empty($_FILES[$k]['name'])
                ) {

                    $has_attachment = true;
                    break;
                }
            }
            if ($has_attachment && isset($form_data['AccountID'])) {
                // $this->insert_or_update_transport_attachments($form_data['AccountID'], $form_data, $userid, $userid, true);
                $this->insert_or_update_transport_attachments(
                    $form_data['AccountID'],
                    $userid,
                    $userid
                );
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


    public function GetNextTransporterCode($ActSubGroupID2)

    {

        $this->db->select('COUNT(AccountID) as transporter_count');
    $this->db->from('tblclients');
     $this->db->join(
            db_prefix() . 'AccountSubGroup2',
            db_prefix() . 'AccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2',
            'left'
        );
        $this->db->where(db_prefix() . 'AccountSubGroup2.IsTransporter', 'Y');

    // Optional — only count broker-type records if needed
    // Uncomment if tblclients contains mixed account types
    // $this->db->where('IsBroker', 'Y');

    $count_result = $this->db->get()->row();

    $transporter_count = $count_result ? intval($count_result->transporter_count) : 0;

    $next_number = $transporter_count + 1;

    // Generate global broker code
    $short_code = 'T';
    $transporter_code = $short_code . sprintf('%05d', $next_number);

    // Category info is optional now
    $transporter_name = '';

    if ($ActSubGroupID2) {
        $this->db->select('SubActGroupName');
        $this->db->from('tblAccountSubGroup2');
        $this->db->where('SubActGroupID', $ActSubGroupID2);
        $category = $this->db->get()->row();

        $transporter_name = $category ? $category->SubActGroupName : '';
    }

    return [
        'next_code'  => $transporter_code,
        'count'      => $transporter_count,
        'transporter_code'=> $transporter_code,
        'transporter_name'=> $transporter_name,
        'short_code' => $short_code
    ];
    }
}
