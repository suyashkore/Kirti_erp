<?php

use app\services\utilities\Arr;

defined('BASEPATH') or exit('No direct script access allowed');

class VehicleMaster_Model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function GetNextVehicleOwnerCode($ActSubGroupID2 = null)
{
    $this->db->select('COUNT(AccountID) as VehicleOwner_count');
    $this->db->from('tblclients');
     $this->db->join(
            db_prefix() . 'AccountSubGroup2',
            db_prefix() . 'AccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2',
            'left'
        );
        $this->db->where(db_prefix() . 'AccountSubGroup2.IsVehicleOwner', 'Y');

    // Optional — only count broker-type records if needed
    // Uncomment if tblclients contains mixed account types
    // $this->db->where('IsBroker', 'Y');

    $count_result = $this->db->get()->row();

    $VehicleOwner_count = $count_result ? intval($count_result->VehicleOwner_count) : 0;

    $next_number = $VehicleOwner_count + 1;

    // Generate global Vehicle Owner code
    $short_code = 'V';
    $VehicleOwner_code = $short_code . sprintf('%05d', $next_number);

    // Category info is optional now
    $VehicleOwner_name = '';

    if ($ActSubGroupID2) {
        $this->db->select('SubActGroupName');
        $this->db->from('tblAccountSubGroup2');
        $this->db->where('SubActGroupID', $ActSubGroupID2);
        $category = $this->db->get()->row();

        $VehicleOwner_name = $category ? $category->SubActGroupName : '';
    }

    return [
        'next_code'  => $VehicleOwner_code,
        'count'      => $VehicleOwner_count,
        'VehicleOwner_code'=> $VehicleOwner_code,
        'VehicleOwner_name'=> $VehicleOwner_name,
        'short_code' => $short_code
    ];
}

public function get_vehicleowner()
    {
        $this->db->select([db_prefix() . 'AccountSubGroup2.*',]);

        $this->db->where(db_prefix() . 'AccountSubGroup2.IsVehicleOwner', 'Y');

        $this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');

        return $this->db->get('tblAccountSubGroup2')->result_array();
    }

    public function get_transporter()
    {
        $this->db->select('tblclients.*');
        $this->db->from('tblclients');
        $this->db->join(
            db_prefix() . 'AccountSubGroup2',
            db_prefix() . 'AccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2',
            'left'
        );
        $this->db->where(db_prefix() . 'AccountSubGroup2.IsTransporter', 'Y');
        $this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');

        return $this->db->get()->result_array();
    }

    public function GetAllVehicleOwnerList()
    {
        $this->db->select('*', FALSE);
        $this->db->from('tblclients');

        $this->db->join('tblAccountSubGroup2', 'tblAccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2', 'LEFT');

        $this->db->where(db_prefix() . 'AccountSubGroup2.IsVehicleOwner', 'Y');
        $this->db->order_by('tblclients.AccountID', 'ASC');

        $result = $this->db->get()->result_array();
        return $result;
    }

    // Get comprehensive account data by AccountID
    public function getComprehensiveAccountDataByID($AccountID)
    {
        $clientDetails = $this->get_AccountDetails($AccountID);
        $bankData = $this->getBankDetailsByAccountID($AccountID);
        $vehicle = $this->getVehicleDetailsByAccountID($AccountID);

        return array(
            'clientDetails' => !empty($clientDetails) ? $clientDetails[0] : array(),
            'bankData' => $bankData,
            'vehicle' => $vehicle,
            // 'attachments' => $attachments
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

    // Vehicle details by AccountID
    public function getVehicleDetailsByAccountID($AccountID)
    {
        $this->db->select(db_prefix() . 'vehicle.*');
        $this->db->where('AccountID', $AccountID);
        return $this->db->get('tblvehicle')->result_array();
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
                    log_message('debug', 'SaveVehicleOwner: ActGroupID fetched for ActSubGroupID1 ' . $insert_data['ActSubGroupID1'] . ' = ' . $group_result->ActGroupID);
                }
            } else {
                log_message('debug', 'SaveVehicleOwner: ActGroupID/ActSubGroupID1 not found for groups_in ' . $form_data['groups_in']);
            }
        }


        // mapping of possible incoming fields -> tblclients columns
        $mapping = [
            'AccountID' => isset($form_data['AccountID']) ? $form_data['AccountID'] : null,
            'PlantID' => $plant,
            'userid' => $user,
            'company' => isset($form_data['AccoountName']) ? $form_data['AccoountName'] : null,
            'PAN' => isset($form_data['Pan']) ? $form_data['Pan'] : null,
            'MobileNo' => isset($form_data['phonenumber']) ? $form_data['phonenumber'] : null,
            'IsTDS' => isset($form_data['Tds']) ? ($form_data['Tds'] == 1 ? 'Y' : 'N') : null,
            'TDSSection' => isset($form_data['Tdsselection']) ? $form_data['Tdsselection'] : null,
            'TDSPer' => isset($form_data['TdsPercent']) ? $form_data['TdsPercent'] : null,

            // Group/Customer Category
            'ActSubGroupID2' => isset($form_data['groups_in']) ? $form_data['groups_in'] : (isset($form_data['ActSubGroupID2']) ? $form_data['ActSubGroupID2'] : null),
            'ActSubGroupID1' => isset($insert_data['ActSubGroupID1']) ? $insert_data['ActSubGroupID1'] : null,
            'ActMainGroupID' => isset($insert_data['ActGroupID']) ? $insert_data['ActGroupID'] : null,

            // Active/Blocked Status
            'IsActive' => isset($form_data['IsActive']) ? ($form_data['IsActive'] == 'Y' ? 'Y' : 'N') : null,

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
                if (isset($form_data['ifsc_code']) && $form_data['ifsc_code'] !== '' && isset($form_data['AccountID'])) {
                    $this->insert_or_update_tblBankMaster($insert_data['AccountID'], $form_data, $user, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
                }

                // Handle vehicle data insertion into tblvehicles
                if (isset($form_data['VehicleData'])  && $form_data['VehicleData'] !== '' && isset($form_data['AccountID'])) {
                    // $this->insert_vehicle_into_tblvehicle($form_data['VehicleData'], $form_data['AccountID'], $plant, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
                    $this->insert_vehicle_into_tblvehicle($insert_data['AccountID'],$form_data['VehicleData'],$user, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0,false);
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

private function insert_vehicle_into_tblvehicle(
    $account_id,
    $vehicle_json,
    $created_by,
    $is_update = false
) {
    try {

        //  Decode JSON payload
        $vehicles = json_decode($vehicle_json, true);

        if (!is_array($vehicles) || empty($vehicles)) {
            log_message('error', 'Vehicle JSON invalid');
            return;
        }

        $upload_path = FCPATH . 'uploads/vehicle_rc/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $now = date('Y-m-d H:i:s');

        // ==================================================
        // Preserve existing RC files BEFORE delete
        // ==================================================
        $existing_rc_map = [];

        if ($is_update) {

            $old_records = $this->db
                ->where('AccountID', $account_id)
                ->get('tblvehicle')
                ->result_array();

            foreach ($old_records as $row) {
                $existing_rc_map[$row['VehicleNo']] = $row['RcBook'];
            }

            // delete old rows
            $this->db->where('AccountID', $account_id);
            $this->db->delete('tblvehicle');
        }

        // ==================================================
        // Insert vehicles
        // ==================================================
        foreach ($vehicles as $index => $vehicle) {

            if (empty($vehicle['VehicleNo'])) continue;

            $vehicle_no = $vehicle['VehicleNo'];

            // restore old RC if exists
            $existing_rc = $existing_rc_map[$vehicle_no] ?? null;
            $rc_path = $existing_rc;

            // ==========================================
            // Handle RC upload
            // ==========================================
            if (
                isset($_FILES['RCBook']['name'][$index]) &&
                $_FILES['RCBook']['error'][$index] === UPLOAD_ERR_OK
            ) {

                $tmp  = $_FILES['RCBook']['tmp_name'][$index];
                $name = $_FILES['RCBook']['name'][$index];

                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

                if (in_array($ext, $allowed)) {

                    // delete old RC ONLY if replacing
                    if ($existing_rc && file_exists(FCPATH . $existing_rc)) {
                        unlink(FCPATH . $existing_rc);
                    }

                    $new_name = 'RC_' . $vehicle_no . '_' . time() . '.' . $ext;

                    if (move_uploaded_file($tmp, $upload_path . $new_name)) {
                        $rc_path = 'uploads/vehicle_rc/' . $new_name;
                    }
                }
            }

            // ==========================================
            // Build insert data
            // ==========================================
            $insert = [

                'AccountID'      => $account_id,
                'VehicleNo'      => strtoupper($vehicle_no),
                'TransporterID'  => $vehicle['PreferTransport'] ?? null,
                'DriverName'     => $vehicle['DriverName'] ?? null,
                'DriverMobileNo' => $vehicle['DriverMobile'] ?? null,
                'LicenceNo'      => $vehicle['LicenceNo'] ?? null,
                'RcBook'         => $rc_path ?? null,
                'Capacity'       => $vehicle['Capacity'] ?? null,
                'VehicleType'    => $vehicle['VehicleType'] ?? null,

                'IsActive'       => $vehicle['VehicleIsActive'] ?? 'Y',

                'UserID'         => $created_by,
                'UserID2'        => $created_by,
                'TransDate'      => $now,
                'Lupdate'        => $now,
            ];

            // remove null/empty values
            $insert = array_filter($insert, fn($v) => $v !== null && $v !== '');

            $this->db->insert('tblvehicle', $insert);

            log_message('debug', 'Vehicle inserted: ' . json_encode($insert));
        }

    } catch (Exception $e) {

        log_message('error', 'Vehicle insert failed: ' . $e->getMessage());
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

            // Handle bank data update into tblBankMaster - SAFE UPDATE (only if is_bank_detail is explicitly set)
            if (isset($form_data['ifsc_code']) && $form_data['ifsc_code'] !== '' && isset($form_data['AccountID']) ) {
                // Update bank data (UPDATE mode - will smart update existing bank records)
                $this->insert_or_update_tblBankMaster($form_data['AccountID'], $form_data, $userid, $userid, true);
            }

            // Vehicle Data update into tblvehicle
            if (isset($form_data['VehicleData']) && $form_data['VehicleData'] !== '' && isset($form_data['AccountID'])) {
               $this->insert_vehicle_into_tblvehicle(
    $form_data['AccountID'],
    $form_data['VehicleData'],
    $userid,
    true
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

    function CheckPanExit($Pan)
		{
			$this->db->select('tblclients.*');
			$this->db->where("PAN" ,$Pan);
			return $this->db->get(db_prefix() . 'clients')->row();
		}
}
