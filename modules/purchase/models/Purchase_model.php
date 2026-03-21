<?php



defined('BASEPATH') or exit('No direct script access allowed');



/**

	* This class describes a purchase model. 

*/

class Purchase_model extends App_Model
{

	private $shipping_fields = ['shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];



	private $contact_columns;



	public function __construct()
	{

		parent::__construct();



		$this->contact_columns = hooks()->apply_filters('contact_columns', ['firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'ticket_emails', 'is_primary']);

	}



	/**

		* Gets the vendor.

		*

		* @param      string        $id     The identifier

		* @param      array|string  $where  The where

		*

		* @return     <type>        The vendor or list vendors.

	*/

	public function get_vendor_bkp($id = '', $where = [])
	{

		$this->db->select(implode(',', prefixed_table_fields_array(db_prefix() . 'pur_vendor')) . ',' . get_sql_select_vendor_company());



		$this->db->join(db_prefix() . 'countries', '' . db_prefix() . 'countries.country_id = ' . db_prefix() . 'pur_vendor.country', 'left');

		$this->db->join(db_prefix() . 'pur_contacts', '' . db_prefix() . 'pur_contacts.userid = ' . db_prefix() . 'pur_vendor.userid AND is_primary = 1', 'left');



		if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {

			$this->db->where($where);

		}



		if (is_numeric($id)) {



			$this->db->where(db_prefix() . 'pur_vendor.userid', $id);

			$vendor = $this->db->get(db_prefix() . 'pur_vendor')->row();



			if ($vendor && get_option('company_requires_vat_number_field') == 0) {

				$vendor->vat = null;

			}





			return $vendor;



		}



		$this->db->order_by('company', 'asc');



		return $this->db->get(db_prefix() . 'pur_vendor')->result_array();

	}



	public function GetAccountList()
	{

		$selected_company = $this->session->userdata('root_company');

		$SubActGroupID = array('100023');

		$this->db->select('SubActGroupID');

		$this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $SubActGroupID);

		$this->db->where(db_prefix() . 'accountgroupssub.IsVendor', 'Y');

		$Data = $this->db->get('tblAccountSubGroup2')->result_array();

		$quotedArray = array_map(function ($row) {

			return (string) $row['SubActGroupID'];

		}, $Data);

		$commaSeparatedSubActGroupIDs = $quotedArray;

		// print_r($commaSeparatedSubActGroupIDs); die;

		$this->db->select('tblclients.AccountID,tblclients.company,tblxx_statelist.state_name,tblxx_citylist.city_name');

		$this->db->join(' tblxx_statelist', ' tblxx_statelist.short_name = tblclients.state', 'LEFT');

		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.city', 'LEFT');

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where_in(db_prefix() . 'clients.SubActGroupID', $commaSeparatedSubActGroupIDs);

		$Data = $this->db->get('tblclients')->result_array();

		return $Data;

	}

	public function GetVendorType()
	{

		$selected_company = $this->session->userdata('root_company');

		$SubActGroupID = array('100023');

		$this->db->select('');

		$this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $SubActGroupID);

		$this->db->where(db_prefix() . 'accountgroupssub.IsVendor', 'Y');

		$Data = $this->db->get('tblAccountSubGroup2')->result_array();

		return $Data;

	}



	public function GetVendorCodeByGroup($vendor_type)
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select(db_prefix() . 'clients.*');

		$this->db->where(db_prefix() . 'clients.SubActGroupID', $vendor_type);

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->order_by(db_prefix() . 'clients.userid', 'DESC');

		return $this->db->get('tblclients')->row();

	}



	public function GetVendorGroupDetails($vendor_type)
	{



		$this->db->select(db_prefix() . 'accountgroupssub.*');

		$this->db->from(db_prefix() . 'accountgroupssub');

		$this->db->where(db_prefix() . 'accountgroupssub.SubActGroupID', $vendor_type);

		$data = $this->db->get()->row();





		return $data;

	}

	public function get_tds_sections()
	{

		$this->db->select(db_prefix() . 'TDSMaster.*');

		$this->db->from(db_prefix() . 'TDSMaster');

		$data = $this->db->get()->result_array();

		return $data;

	}



	public function GetAccountDetails($AccountID)
	{

		$selected_company = $this->session->userdata('root_company');

		$SubActGroupID = array('100023');

		$this->db->select('tblclients.AccountID,tblclients.company');

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'clients.AccountID', $AccountID);

		$this->db->where_in(db_prefix() . 'clients.SubActGroupID1', $SubActGroupID);

		$Data = $this->db->get('tblclients')->row();

		return $Data;

	}



	public function GetItemList($MainGroupID = "", $Subgroup = "", $Subgroup2 = "")
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('tblitems.ItemID,tblitems.description,tblitems.hsn_code,tblItemsDivisionMaster.name AS DivisionName,tblitemsSubGroup2.name AS SubGroupName,tblitems_main_groups.name AS MainGroupName');

		$this->db->join(' tblItemsDivisionMaster', ' tblItemsDivisionMaster.id = tblitems.DivisionID', 'LEFT');

		$this->db->join('tblitemsSubGroup2', 'tblitemsSubGroup2.id = tblitems.SubGrpID1', 'LEFT');

		$this->db->join('tblitems_main_groups', 'tblitems_main_groups.id = tblitemsSubGroup2.main_DivisionID', 'LEFT');

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		if ($MainGroupID) {

			$this->db->where(db_prefix() . 'items.MainGrpID', $MainGroupID);

		}

		if ($Subgroup) {

			$this->db->where(db_prefix() . 'items.SubGrpID1', $Subgroup);

		}

		if ($Subgroup2) {

			$this->db->where(db_prefix() . 'items.SubGrpID2', $Subgroup2);

		}

		$Data = $this->db->get('tblitems')->result_array();

		return $Data;

	}



	public function GetItemDetails($ItemID)
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('tblitems.ItemID,tblitems.description,tblitems.unit,tblitems.case_qty');

		$this->db->where(db_prefix() . 'items.ItemID', $ItemID);

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$Data = $this->db->get('tblitems')->row();

		return $Data;
	}

	/**
	 * Get item categories for a given ItemType from tblItemCategoryMaster
	 *
	 * @param int $ItemType
	 * @return array
	 */
	public function GetItemCategoriesByType($ItemType)
	{
		$this->db->select('*');
		$this->db->from('tblItemCategoryMaster');
		$this->db->where('ItemType', $ItemType);
		$this->db->where('IsActive', 'Y');
		$this->db->order_by('CategoryName', 'ASC');
		$result = $this->db->get()->result_array();
		return $result;
	}



	public function GetVendorItemByVendor($AccountID, $ItemID)
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('*');

		$this->db->where(db_prefix() . 'VendorWiseItems.ItemID', $ItemID);

		$this->db->where(db_prefix() . 'VendorWiseItems.AccountID', $AccountID);

		$Data = $this->db->get('tblVendorWiseItems')->row();

		return $Data;

	}



	//-----------------------------------------------------

	public function GetCityList($id)
	{

		$query = $this->db->get_where('tblxx_citylist', array('state_id' => $id));

		return $result = $query->result_array();

	}

	public function get_vendor($id = '', $where = [])
	{



		$selected_company = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');



		$this->db->select('*,' . db_prefix() . 'clients.CstNo as Cst_No,' . db_prefix() . 'clients.phonenumber as phone_number,' . db_prefix() . 'accountbalances.BAL1');

		$this->db->join(db_prefix() . 'contacts', '' . db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'clients.PlantID AND  ' . db_prefix() . 'clients.PlantID = ' . $selected_company, 'left');

		$this->db->join(db_prefix() . 'accountbalances', '' . db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND  ' . db_prefix() . 'clients.PlantID = ' . $selected_company, 'left');



		/* if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {

			$this->db->where($where);

		}*/



		if ($id) {

			$this->db->where(db_prefix() . 'clients.AccountID', $id);

			$this->db->where(db_prefix() . 'accountbalances.FY', $FY);

			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

			$client = $this->db->get(db_prefix() . 'clients')->row();



			if ($client && get_option('company_requires_vat_number_field') == 0) {

				$client->vat = null;

			}



			$GLOBALS['client'] = $client;



			return $client;

		}



		$this->db->order_by('company', 'asc');

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		return $this->db->get(db_prefix() . 'clients')->result_array();

	}



	public function get_data_vendor($id = '')
	{

		//   return $this->db->get_where('clients',array('userid' =>$id))->row();

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'clients');

		$this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'xx_citylist.id = ' . db_prefix() . 'clients.city', 'left');

		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');

		$this->db->join(db_prefix() . 'TDSMaster', db_prefix() . 'TDSMaster.TDSCode = ' . db_prefix() . 'clients.TDSSection', 'left');

		$this->db->join(db_prefix() . 'accountbalances', db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND ' . db_prefix() . 'accountbalances.FY ="' . $year . '"', 'left');

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'clients.AccountID', $id);



		$data = $this->db->get()->row();

		if ($data) {

			$data->items = $this->ItemAssocToVendor($data->AccountID);

			$data->Listitems = $this->GetVendorWiseItems($data->AccountID);

		}

		return $data;

	}



	public function GetVendorList($id = '')
	{

		$selected_company = $this->session->userdata('root_company');

		$this->db->select('tblclients.AccountID, tblclients.company, tblclients.FavouringName, tblclients.PAN, tblclients.GSTIN, tblclients.OrganisationType, tblclients.GSTType, tblclients.IsActive', FALSE);

		// $this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		// $this->db->where(db_prefix() . 'clients.SubActGroupID1', "100056");

		// $this->db->join('tblcontacts', 'tblcontacts.AccountID = tblclients.AccountID AND tblcontacts.PlantID = tblclients.PlantID','LEFT');

		// $this->db->join('tblcustomers_groups', 'tblcustomers_groups.id = tblclients.DistributorType','LEFT');

		// $this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclients.city','LEFT');

		// $this->db->join('tblxx_statelist', 'tblxx_statelist.short_name = tblclients.state','LEFT');

		$result = $this->db->get('tblclients')->result_array();

		return $result;



	}



	// public function GetVendorListNEW($id = '')
	// {



	// 	$selected_company = $this->session->userdata('root_company');

	// 	$year = $_SESSION['finacial_year'];

	// 	$this->db->select(db_prefix() . 'clients.*,' . db_prefix() . 'accountbalances.BAL1,' . db_prefix() . 'contacts.FLNO1,' . db_prefix() . 'contacts.email,' . db_prefix() . 'contacts.Pan,' . db_prefix() . 'contacts.Aadhaarno');

	// 	$this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'contacts.PlantID = ' . db_prefix() . 'clients.PlantID', 'left');

	// 	$this->db->join(db_prefix() . 'accountbalances', db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND ' . db_prefix() . 'accountbalances.FY ="' . $year . '"', 'left');

	// 	$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

	// 	$this->db->where(db_prefix() . 'clients.AccountID', $id);

	// 	$Data = $this->db->get(db_prefix() . 'clients')->row();

	// 	if ($Data) {

	// 		$Data->AccountType = 'Client';

	// 		$cityList = $this->GetCityList($Data->state);

	// 		$Data->cityList = $cityList;



	// 		$this->db->select('tblVendorWiseItems.*,tblitems.description,tblitems.unit,tblitems.case_qty as CaseQty');

	// 		$this->db->from(db_prefix() . 'VendorWiseItems');

	// 		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'VendorWiseItems.ItemID AND ' . db_prefix() . 'items.PlantID = ' . $selected_company . '', 'left');

	// 		$this->db->where('AccountID', $id);

	// 		$ItemData = $this->db->get()->result();



	// 		$Data->ItemData = $ItemData;



	// 		$this->db->select('tblVendorWiseDiscPercentage.*');

	// 		$this->db->from(db_prefix() . 'VendorWiseDiscPercentage');

	// 		$this->db->where('AccountID', $id);

	// 		$DiscData = $this->db->get()->result();



	// 		$Data->DiscData = $DiscData;

	// 		$Data->TDSPerList = $this->gettdspercent_new($Data->TDSSection);

	// 	} else {

	// 		$regExp = '.*;s:[0-9]+:"' . $selected_company . '".*';

	// 		$this->db->select(db_prefix() . 'staff.*,');

	// 		//$this->db->where('tblstaff.staff_comp REGEXP',$regExp);

	// 		$this->db->where(db_prefix() . 'staff.AccountID', $id);

	// 		$this->db->where(db_prefix() . 'staff.PlantID', $selected_company);

	// 		$Data = $this->db->get(db_prefix() . 'staff')->row();

	// 		if ($Data) {

	// 			$Data->AccountType = 'Staff';

	// 		}

	// 	}

	// 	return $Data;



	// }



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

	public function getShippingDataByAccountID($AccountID)
	{
		$this->db->where('AccountID', $AccountID);
		return $this->db->get('tblclientwiseshippingdata')->result_array();
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
	 * Fetch bank details from tblBankMaster by AccountID
	 * @param  string $AccountID Client Account ID
	 * @return array Bank details rows
	 */
	public function getBankDetailsByAccountID($AccountID)
	{
		$this->db->where('AccountID', $AccountID);
		return $this->db->get('tblBankMaster')->result_array();
	}

	public function getVendorDetailByAccountID($AccountID)
	{
		$this->db->select(
			'AccountID,
			company,
			GSTIN as gst_number,
			PAN as pan,
			TDSSection as TDS,
			IsTDS as is_tds_applicable,
			TDSPer as tds_percent,
			tblxx_citylist.city_name as city,
			billing_zip as postal_code,
			billing_address as address,
			tblxx_statelist.state_name as state,
			tblcountries.long_name as country'
		);

		$this->db->from(db_prefix() . 'clients');

		$this->db->join('tblxx_statelist', 'clients.billing_state = tblxx_statelist.short_name', 'left');
		$this->db->join('tblcountries', 'clients.billing_country = tblcountries.country_id', 'left');
		$this->db->join('tblxx_citylist', 'clients.billing_city = tblxx_citylist.id', 'left');
		// $this->db->join('tbltdsdetails', 'clients.TDSSection = tbltdsdetails.TDSCode', 'left');

		$this->db->where(db_prefix() . 'clients.AccountID', $AccountID);

		$result = $this->db->get()->result_array();

		if (!empty($result)) {
			return array(
				'gst_no' => $result[0]['gst_number'] ?? '',
				'pan' => $result[0]['pan'] ?? '',
				'country' => $result[0]['country'] ?? '',
				'state' => $result[0]['state'] ?? '',
				'city' => $result[0]['city'] ?? '',
				'postal_code' => $result[0]['postal_code'] ?? '',
				'address' => $result[0]['address'] ?? '',
				'company' => $result[0]['company'] ?? '',
				'TDS' => $result[0]['TDS'] ?? '',
				'is_tds_applicable' => $result[0]['is_tds_applicable'] ?? '',
				'tds_percent' => $result[0]['tds_percent'] ?? '',

			);
		}

		return array();
	}

	/**
	 * Fetch item details from tblitems by ItemID
	 * @param  int $item_id Item ID
	 * @return array Item data with hsn_code, unit, UnitWeight, and tax
	 */
	public function getItemDetailsById($item_id)
	{
		// Select item fields and join taxes to get tax rate
		$this->db->select(
			db_prefix() . "items.ItemID,
			" . db_prefix() . "items.ItemName,
			" . db_prefix() . "items.hsn_code,
			tblUnitMaster.ShortCode as unit,
			" . db_prefix() . "items.UnitWeight,
			tbltaxes.taxrate as tax"
		);

		$this->db->from(db_prefix() . 'items');
		// join taxes table to fetch tax rate (tbltaxes.id = items.tax)
		$this->db->join('tbltaxes', 'tbltaxes.id = ' . db_prefix() . "items.tax", 'left');
		// join unit master to get ShortCode for unit (tblUnitMaster.id = items.unit)
		$this->db->join('tblUnitMaster', 'tblUnitMaster.id = ' . db_prefix() . "items.unit", 'left');
		$this->db->where(db_prefix() . 'items.ItemID', $item_id);

		$result = $this->db->get()->result_array();

		if (!empty($result)) {
			return array(
				'hsn_code' => $result[0]['hsn_code'] ?? '',
				'unit' => $result[0]['unit'] ?? '',
				'UnitWeight' => $result[0]['UnitWeight'] ?? '0',
				'tax' => $result[0]['tax'] ?? '0',
			);
		}

		return array();
	}

	/**
	 * Fetch shipping/location data from tblclientwiseshippingdata by AccountID
	 * @param  string $AccountID Client Account ID
	 * @return array Shipping data rows with city and state names
	 */
	public function getShippingDatacity($AccountID)
	{
		$this->db->select(
			'tblclientwiseshippingdata.id,
			tblxx_citylist.city_name'
		);

		$this->db->from('tblclientwiseshippingdata');
		$this->db->join('tblxx_citylist', 'tblxx_citylist.id = tblclientwiseshippingdata.ShippingCity', 'LEFT');
		$this->db->where('tblclientwiseshippingdata.AccountID', $AccountID);

		return $this->db->get()->result_array();
	}

	public function get_AccountDetails($AccountID)
{
    $this->db->select(
        db_prefix() . 'clients.*,' .
        db_prefix() . 'countries.country_id,' .
        db_prefix() . 'countries.long_name as country_name,' .
        db_prefix() . 'countries.iso2 as country_iso2,' .
        db_prefix() . 'countries.calling_code as country_calling_code'
    );

    $this->db->from(db_prefix() . 'clients');

    $this->db->join(
        db_prefix() . 'countries',
        db_prefix() . 'countries.country_id = ' . db_prefix() . 'clients.billing_country',
        'left'
    );

    $this->db->where(db_prefix() . 'clients.AccountID', $AccountID);

    return $this->db->get()->result_array();
}


	public function gettdspercent_new($Tdsselection)
	{



		$this->db->select(db_prefix() . 'TDSDetails.*');

		$this->db->where(db_prefix() . 'TDSDetails.TDSCode', $Tdsselection);

		$this->db->from(db_prefix() . 'TDSDetails');

		return $this->db->get()->result_array();

	}



	// Add New Vendor

	public function SaveVendor($form_data)
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
		$user = isset($form_data['userid']) ? $form_data['userid'] : $this->session->userdata('staff_user_id');

		// Initialize insert data array
		$insert_data = [];

		// Fetch ActSubGroupID1 and ActGroupID using JOIN query
		if (isset($form_data['vendor_type']) && !empty($form_data['vendor_type'])) {
			$this->db->select('sg2.SubActGroupID1, sg1.ActGroupID');
			$this->db->from('tblAccountSubGroup2 sg2');
			$this->db->join('tblAccountSubGroup1 sg1', 'sg2.SubActGroupID1 = sg1.SubActGroupID1', 'left');
			$this->db->where('sg2.SubActGroupID', $form_data['vendor_type']);
			$group_result = $this->db->get()->row();

			if ($group_result) {
				if (isset($group_result->SubActGroupID1)) {
					$insert_data['ActSubGroupID1'] = $group_result->SubActGroupID1;
					log_message('debug', 'SaveVendor: ActSubGroupID1 fetched for vendor_type ' . $form_data['vendor_type'] . ' = ' . $group_result->SubActGroupID1);
				}
				if (isset($group_result->ActGroupID)) {
					$insert_data['ActGroupID'] = $group_result->ActGroupID;
					log_message('debug', 'SaveVendor: ActGroupID fetched for ActSubGroupID1 ' . $insert_data['ActSubGroupID1'] . ' = ' . $group_result->ActGroupID);
				}
			} else {
				log_message('debug', 'SaveVendor: ActGroupID/ActSubGroupID1 not found for vendor_type ' . $form_data['vendor_type']);
			}
		}

		// mapping of possible incoming fields -> tblclients columns
		$mapping = [
			'AccountID' => isset($form_data['AccountID']) ? $form_data['AccountID'] : null,
			'PlantID' => $plant,
			'userid' => $user,
			// Account / Company fields - match DB column names
			'company' => isset($form_data['AccountName']) ? $form_data['AccountName'] : (isset($form_data['company']) ? $form_data['company'] : null),
			'FavouringName' => isset($form_data['favouring_name']) ? $form_data['favouring_name'] : null,
			'PAN' => isset($form_data['Pan']) ? $form_data['Pan'] : (isset($form_data['Pan']) ? $form_data['Pan'] : null),
			'GSTIN' => isset($form_data['vat']) ? $form_data['vat'] : null,
			'billing_country' => (isset($form_data['billing_country']) && is_numeric($form_data['billing_country'])) ? (int) $form_data['billing_country'] : (isset($form_data['billing_country']) && is_numeric($form_data['billing_country']) ? (int) $form_data['billing_country'] : null),
			'billing_state' => isset($form_data['state']) ? $form_data['state'] : null,
			'billing_city' => isset($form_data['city']) ? $form_data['city'] : null,
			'billing_zip' => isset($form_data['zip']) ? $form_data['zip'] : null,
			'billing_address' => isset($form_data['address']) ? $form_data['address'] : null,
			'MobileNo' => isset($form_data['phonenumber']) ? $form_data['phonenumber'] : null,
			'AltMobileNo' => isset($form_data['telephone']) ? $form_data['telephone'] : null,
			'Email' => isset($form_data['email']) ? $form_data['email'] : null,
			'IsTDS' => isset($form_data['Tds']) ? ($form_data['Tds'] == 1 ? 'Y' : 'N') : null,
			'TDSSection' => isset($form_data['Tdsselection']) ? $form_data['Tdsselection'] : null,
			'TDSPer' => isset($form_data['TdsPercent']) ? $form_data['TdsPercent'] : null,
			'default_currency' => isset($form_data['default_currency']) ? $form_data['default_currency'] : null,

			// Billing Information fields
			'OrganisationType' => isset($form_data['organisation_type']) ? $form_data['organisation_type'] : null,
			'GSTType' => isset($form_data['gsttype']) ? $form_data['gsttype'] : (isset($form_data['gst_type']) ? $form_data['gst_type'] : null),

			// Credit / Payment / Bank Information fields
			'PaymentTerms' => isset($form_data['pay_term']) ? $form_data['pay_term'] : (isset($form_data['pay_term']) ? $form_data['pay_term'] : null),
			'PaymentCycle' => isset($form_data['payment_cycle']) ? $form_data['payment_cycle'] : null,
			'PaymentCycleType' => isset($form_data['payment_cycle_type']) ? $form_data['payment_cycle_type'] : null,
			'GraceDay' => isset($form_data['credit_days']) ? $form_data['credit_days'] : null,
			'CreditLimit' => isset($form_data['MaxCrdAmt']) ? $form_data['MaxCrdAmt'] : null,
			'FreightTerms' => isset($form_data['freight_terms']) ? $form_data['freight_terms'] : (isset($form_data['freight_term']) ? $form_data['freight_term'] : null),

			// Other Information fields
			'TAN' => isset($form_data['tan_number']) ? $form_data['tan_number'] : null,
			'PriorityID' => isset($form_data['priority']) ? $form_data['priority'] : null,
			'FSSAINo' => isset($form_data['FLNO1']) ? $form_data['FLNO1'] : null,
			'FSSAIExpiry' => isset($form_data['expiry_licence']) ? $form_data['expiry_licence'] : null,
			'TerritoryID' => isset($form_data['territory']) ? $form_data['territory'] : null,
			'website' => isset($form_data['website']) ? $form_data['website'] : null,
			'Attachment' => isset($form_data['attachment']) ? $form_data['attachment'] : (isset($form_data['Attachment']) ? $form_data['Attachment'] : null),
			'AdditionalInfo' => isset($form_data['additional_info']) ? $form_data['additional_info'] : null,

			// Group/Customer Category (map vendor_type to ActMainGroupID as requested)
			// 'ActMainGroupID' => isset($form_data['vendor_type']) ? $form_data['vendor_type'] : (isset($form_data['groups_in']) ? $form_data['groups_in'] : null),
			'ActSubGroupID2' => isset($form_data['vendor_type']) ? $form_data['vendor_type'] : (isset($form_data['ActSubGroupID2']) ? $form_data['ActSubGroupID2'] : null),
			'ActSubGroupID1' => isset($insert_data['ActSubGroupID1']) ? $insert_data['ActSubGroupID1'] : null,
			'ActMainGroupID' => isset($insert_data['ActGroupID']) ? $insert_data['ActGroupID'] : null,


			// Active/Blocked Status
			'IsActive' => isset($form_data['active']) ? $form_data['active'] : '',
			'DeActiveReason' => isset($form_data['blocked_reason']) ? $form_data['blocked_reason'] : null,

			'CreatedBy' => is_staff_logged_in() ? get_staff_user_id() : 0,
			'TransDate' => date('Y-m-d H:i:s'),
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

		// If AccountID missing, try to auto-generate from vendor_type when provided
		if (!isset($insert_data['AccountID']) || empty($insert_data['AccountID'])) {
			if (isset($form_data['vendor_type']) && !empty($form_data['vendor_type'])) {
				$generated = 'V' . $form_data['vendor_type'] . '_' . time();
				log_message('debug', 'SaveVendor: auto-generated AccountID=' . $generated);
				$insert_data['AccountID'] = $generated;
			} else {
				log_message('error', 'SaveVendor failed: missing AccountID and vendor_type. form_data keys: ' . implode(',', array_keys($form_data)));
				return ['success' => false, 'message' => 'AccountID (Vendor Code) is required'];
			}
		}

		try {
			$this->db->insert('tblclients', $insert_data);
			if ($this->db->affected_rows() > 0) {
				// If bank details were provided, insert into tblBankMaster as well (INSERT mode)
				if (isset($form_data['is_bank_detail']) && $form_data['is_bank_detail']) {
					$this->insert_or_update_tblBankMaster($insert_data['AccountID'], $form_data, $user, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
				}

				// Handle contact data insertion into tblcontacts
				if (isset($form_data['ContactData']) && !empty($form_data['ContactData'])) {
					$this->insert_contacts_into_tblcontacts($form_data['ContactData'], $insert_data['AccountID'], $plant, isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
				}

				// Handle shipping/location data insertion into tblclientwiseshippingdata
				$shippingPayload = null;
				if (isset($form_data['LocationData']) && !empty($form_data['LocationData'])) {
					$shippingPayload = $form_data['LocationData'];
				} elseif (isset($form_data['ShippingData']) && !empty($form_data['ShippingData'])) {
					$shippingPayload = $form_data['ShippingData'];
				}

				if ($shippingPayload !== null) {
					$this->insert_shipping_data_into_tblclientwiseshippingdata($shippingPayload, $insert_data['AccountID'], isset($insert_data['CreatedBy']) ? $insert_data['CreatedBy'] : 0, false);
				}


				// Return the AccountID back to caller (frontend expects this)
				return $insert_data['AccountID'];

			}
		} catch (Exception $e) {
			log_message('error', 'tblclients insert error: ' . $e->getMessage());
			log_message('error', 'tblclients insert_data: ' . json_encode($insert_data));
			return ['success' => false, 'message' => 'Database error while inserting client: ' . $e->getMessage()];
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
				log_message('debug', message: 'Deleted existing contacts for AccountID: ' . $account_id);
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
					log_message('warning', 'Skipping contact insertion - missing AccountID for index ' . $index);
					continue;
				}

				if (empty($contact_insert['firstname'])) {
					log_message('warning', 'Skipping contact insertion - missing firstname for index ' . $index);
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
					log_message('error', 'Skipping shipping data insertion - missing AccountID for index ' . $index);
					continue;
				}

				if (empty($shipping_insert['ShippingState']) || empty($shipping_insert['ShippingCity'])) {
					log_message('error', 'Skipping shipping data insertion - missing State or City for index ' . $index);
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
				'BranchName' => isset($bank_data['bank_branch']) ? $bank_data['bank_branch'] : (isset($bank_data['branch_name']) ? $bank_data['branch_name'] : null),
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


	// Update Exiting Vendor

	// public function UpdateVendor($Clientdata, $Contactdata, $Balancedata, $AccountID, $itemdata, $DisData)
	// {

	// 	$selected_company = $this->session->userdata('root_company');

	// 	$FY = $this->session->userdata('finacial_year');



	// 	$this->db->where('AccountID', $AccountID);

	// 	$this->db->where('PlantID', $selected_company);

	// 	$this->db->update(db_prefix() . 'clients', $Clientdata);

	// 	$UPDATE = $this->db->affected_rows();

	// 	// Replace existing contact rows for this vendor with provided contact list
	// 	$this->db->where('AccountID', $AccountID);
	// 	$this->db->where('PlantID', $selected_company);
	// 	$this->db->delete(db_prefix() . 'contacts');

	// 	if (is_array($Contactdata) && count($Contactdata) > 0) {
	// 		// Ensure AccountID and PlantID set on each row
	// 		foreach ($Contactdata as &$cd) {
	// 			$cd['AccountID'] = strtoupper($AccountID);
	// 			$cd['PlantID'] = $selected_company;
	// 		}
	// 		unset($cd);
	// 		$this->db->insert_batch(db_prefix() . 'contacts', $Contactdata);
	// 	}



	// 	$CheckACTBALRecord = $this->ChkActBalRecord($AccountID);

	// 	$staff_user_id = $this->session->userdata('staff_user_id');

	// 	if ($CheckACTBALRecord) {

	// 		if ($staff_user_id == "3") {

	// 			$this->db->where('PlantID', $selected_company);

	// 			$this->db->where('FY', $FY);

	// 			$this->db->where('AccountID', $AccountID);

	// 			$this->db->update(db_prefix() . 'accountbalances', $Balancedata);

	// 			$UPDATE = $this->db->affected_rows();

	// 		}

	// 	} else {

	// 		$Balancedata['AccountID'] = $AccountID;

	// 		$Balancedata['PlantID'] = $selected_company;

	// 		$Balancedata['FY'] = $FY;

	// 		$this->db->insert(db_prefix() . 'accountbalances', $Balancedata);

	// 	}



	// 	foreach ($itemdata as $index => $item) {

	// 		$ItemID = $item['ItemID'];

	// 		$DeliveryDays = $item['DeliveryDays'];

	// 		$status = $item['item_status'];



	// 		$chk = $this->GetVendorItemByVendor($AccountID, $ItemID);

	// 		if (!empty($chk)) {

	// 			$update_arr = [

	// 				'status' => $status,

	// 				'DeliveryDays' => $DeliveryDays,

	// 			];

	// 			$this->db->where('ItemID', $ItemID);

	// 			$this->db->where('AccountID', $AccountID);

	// 			$this->db->update(db_prefix() . 'VendorWiseItems', $update_arr);

	// 		} else {

	// 			$insert_arr = [

	// 				'ItemID' => $ItemID,

	// 				'AccountID' => $this->input->post('AccountID'),

	// 				'DeliveryDays' => $DeliveryDays,

	// 				'status' => $status,

	// 				'Transdate' => date('Y-m-d H:i:s'),

	// 				'UserID' => $this->session->userdata('username'),

	// 			];

	// 			$this->db->insert(db_prefix() . 'VendorWiseItems', $insert_arr);

	// 		}

	// 	}





	// 	$this->db->where('AccountID', $AccountID);

	// 	$this->db->delete(db_prefix() . 'VendorWiseDiscPercentage');



	// 	$this->db->insert_batch(db_prefix() . 'VendorWiseDiscPercentage', $DisData);



	// 	if ($UPDATE > 0) {

	// 		return true;

	// 	} else {

	// 		return false;

	// 	}

	// }

	//      * Update data in tblclients table

	//  * @param  array $form_data Form POST data

	//  * @param  integer $userid User ID

	//  * @return mixed boolean

	//  */

	public function update_tblclients($form_data, $userid = 0)
	{
		// $this->db->truncate('tblclients');
		// $this->db->truncate('tblBankMaster');
		// $this->db->truncate('tblcontacts');
		// $this->db->truncate('tblclientwiseshippingdata');
		// die;

		// Map form fields to database column names - only non-null values
		// echo"";
		// print_r($form_data);die;
// echo"<pre>";
// print_r($form_data);
// die();

		$data = [

			'company' => isset($form_data['AccoountName']) ? $form_data['AccoountName'] : null,

			'FavouringName' => isset($form_data['favouring_name']) ? $form_data['favouring_name'] : null,

			'PAN' => isset($form_data['Pan']) ? $form_data['Pan'] : null,

			'GSTIN' => isset($form_data['vat']) ? $form_data['vat'] : null,

			'OrganisationType' => isset($form_data['organisation_type']) ? $form_data['organisation_type'] : null,

			'GSTType' => isset($form_data['gsttype']) ? $form_data['gsttype'] : null,

			'billing_country' => isset($form_data['billing_country']) ? $form_data['billing_country'] : null,
			// 'billing_country' => (isset($form_data['billing_country']) && is_numeric($form_data['billing_country'])) ? (int) $form_data['billing_country'] : (isset($form_data['billing_country']) && is_numeric($form_data['billing_country']) ? (int) $form_data['billing_country'] : null),

			'billing_state' => isset($form_data['state']) ? $form_data['state'] : null,

			'billing_city' => isset($form_data['city']) ? $form_data['city'] : null,

			'billing_zip' => isset($form_data['zip']) ? $form_data['zip'] : null,

			'billing_address' => isset($form_data['address']) ? $form_data['address'] : null,

			'MobileNo' => isset($form_data['phonenumber']) ? $form_data['phonenumber'] : null,

			'AltMobileNo' => isset($form_data['telephone']) ? $form_data['telephone'] : null,

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

			'IsActive' => isset($form_data['active']) ? $form_data['active'] : '',

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

			// Use AccountID for WHERE clause if provided, otherwise use userid
			if (isset($form_data['AccountID']) && !empty($form_data['AccountID'])) {
				$this->db->where('AccountID', $form_data['AccountID']);
				log_message('debug', 'update_tblclients using AccountID: ' . $form_data['AccountID']);
			} else {
				$this->db->where('userid', $userid);
				log_message('debug', 'update_tblclients using userid: ' . $userid);
			}

			$this->db->update('tblclients', $update_data);
			log_message('debug', 'update_tblclients affected rows: ' . $this->db->affected_rows());


			// Handle contact data update into tblcontacts (UPDATE mode - deletes old records first)
			if (isset($form_data['ContactData']) && !empty($form_data['ContactData']) && isset($form_data['AccountID'])) {
				$plant_id = isset($form_data['PlantID']) ? $form_data['PlantID'] : null;
				$this->insert_contacts_into_tblcontacts($form_data['ContactData'], $form_data['AccountID'], $plant_id, $userid, true);
			}

			// Handle shipping data update into tblclientwiseshippingdata (UPDATE mode - deletes old records first)
			if (isset($form_data['ShippingData']) && !empty($form_data['ShippingData']) && isset($form_data['AccountID'])) {
				$this->insert_shipping_data_into_tblclientwiseshippingdata($form_data['ShippingData'], $form_data['AccountID'], $userid, true);
			}

			// Handle bank data update into tblBankMaster (UPDATE mode - deletes old records first)
			if (isset($form_data['is_bank_detail']) && $form_data['is_bank_detail'] && isset($form_data['AccountID'])) {
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


	// Item Assoc To Vendor

	public function ItemAssocToVendor($AccountID)
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('tblVendorWiseItems.*,tblitems.description,tblitems.unit,tblitems.case_qty as CaseQty');

		$this->db->from(db_prefix() . 'VendorWiseItems');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'VendorWiseItems.ItemID AND ' . db_prefix() . 'items.PlantID = ' . $selected_company . '', 'INNER');

		$this->db->where('AccountID', $AccountID);

		$this->db->where('status', 'Y');

		$result = $this->db->get()->result();



		// Extract the ItemID into an array

		$item_ids = array_column($result, 'ItemID');



		// Convert the array of ItemIDs into a comma-separated string

		$comma_separated_item_ids = implode(',', $item_ids);



		// Return the comma-separated string

		return $comma_separated_item_ids;



	}



	// Check Account Contact Type

	public function ChkContactRecord($AccountID)
	{

		$selected_company = $this->session->userdata('root_company');

		$this->db->select(db_prefix() . 'contacts.*');

		$this->db->where('AccountID', $AccountID);

		$this->db->where('PlantID', $selected_company);

		$this->db->from(db_prefix() . 'contacts');

		$data = $this->db->get()->row();

		return $data;

	}

	// Check Account Contact Type

	public function ChkActBalRecord($AccountID)
	{

		$selected_company = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');

		$this->db->select(db_prefix() . 'accountbalances.*');

		$this->db->where('AccountID', $AccountID);

		$this->db->where('PlantID', $selected_company);

		$this->db->where('FY', $FY);

		$this->db->from(db_prefix() . 'accountbalances');

		$data = $this->db->get()->row();

		return $data;

	}

	public function get_vendor_data($id = '', $where = [])
	{



		$selected_company = $this->session->userdata('root_company');


		$this->db->select(db_prefix() . 'clients.company,' . db_prefix() . 'clients.userid,' . db_prefix() . 'clients.AccountID,');


		$this->db->where_in(db_prefix() . 'clients.SubActGroupID1', ['100023']);

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->order_by('company', 'asc');

		return $this->db->get(db_prefix() . 'clients')->result_array();

	}

	public function GetRMVendor($id = '', $where = [])
	{

		$SubActGroupID = array('100023');

		$this->db->select('SubActGroupID');

		$this->db->where_in(db_prefix() . 'AccountSubGroup2.SubActGroupID1', $SubActGroupID);

		$this->db->where(db_prefix() . 'AccountSubGroup2.IsVendor', 'Y');

		$Data = $this->db->get('tblAccountSubGroup2')->result_array();

		$quotedArray = array_map(function ($row) {

			return (string) $row['SubActGroupID'];

		}, $Data);

		$commaSeparatedSubActGroupIDs = $quotedArray;



		$selected_company = $this->session->userdata('root_company');

		$this->db->select(db_prefix() . 'clients.company,' . db_prefix() . 'clients.userid,' . db_prefix() . 'clients.AccountID,');



		$this->db->where_in(db_prefix() . 'clients.ActSubGroupID2', $commaSeparatedSubActGroupIDs);

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->order_by('company', 'asc');

		return $this->db->get(db_prefix() . 'clients')->result_array();

	}







	public function get_contacts($vendor_id = '', $where = ['active' => 1])
	{

		$this->db->where($where);

		if ($vendor_id != '') {

			$this->db->where('userid', $vendor_id);

		}

		$this->db->order_by('is_primary', 'DESC');



		return $this->db->get(db_prefix() . 'pur_contacts')->result_array();

	}



	/**

		* Gets the contact.

		*

		* @param      <type>  $id     The identifier

		*

		* @return     <type>  The contact.

	*/

	public function get_contact($id)
	{

		$this->db->where('id', $id);



		return $this->db->get(db_prefix() . 'pur_contacts')->row();

	}



	/**

		* Gets the primary contacts.

		*

		* @param      <type>  $id     The identifier

		*

		* @return     <type>  The primary contacts.

	*/

	public function get_primary_contacts($id)
	{

		$this->db->where('userid', $id);

		$this->db->where('is_primary', 1);

		return $this->db->get(db_prefix() . 'pur_contacts')->row();

	}



	/**

		* Adds a vendor.

		*

		* @param      <type>   $data       The data

		* @param      integer  $client_id  The client identifier

		*

		* @return     integer  ( id vendor )

	*/

	// public function add_vendor($data, $client_id = null,$client_or_lead_convert_request = false)

	// {



	// 	// From customer profile register

	// 	if (isset($data['vendor_code'])) {

	// 		$selected_company = $this->session->userdata('root_company');

	// 		$last_year = $this->session->userdata('finacial_year');

	// 		$client['AccountID'] = $data['vendor_code'];

	// 		$client['SubActGroupID'] = $data['account_group'];

	// 		$client['DistributorType'] = '24';

	// 		$accountbalances['AccountID'] = $data['vendor_code'];

	// 		$contacts['AccountID'] = $data['vendor_code'];

	// 		$accountbalances['PlantID'] = $selected_company;

	// 		$client['PlantID'] = $selected_company;

	// 		$contacts['PlantID'] = $selected_company;

	// 		$accountbalances['FY'] = $last_year;

	// 		unset($data['vendor_code']);

	// 	}



	// 	if (isset($data['company'])) {

	// 		$client['company'] = $data['company'];

	// 		unset($data['company']);

	// 	}

	// 	if (isset($data['address'])) {

	// 		$client['address'] = $data['address'];

	// 		unset($data['address']);

	// 	}



	// 	if (isset($data['state'])) {

	// 		$client['state'] = $data['state'];

	// 		unset($data['state']);

	// 	}

	// 	if (isset($data['city'])) {

	// 		$client['city'] = $data['city'];

	// 		unset($data['city']);

	// 	}

	// 	if (isset($data['phonenumber'])) {

	// 		$client['altphonenumber'] = $data['phonenumber'];

	// 		unset($data['phonenumber']);

	// 	}

	// 	if (isset($data['address2'])) {

	// 		$client['Address3'] = $data['address2'];

	// 		unset($data['address2']);

	// 	}

	// 	if (isset($data['zip'])) {

	// 		$client['zip'] = $data['zip'];

	// 		unset($data['zip']);

	// 	}





	// 	if (isset($data['email'])) {

	// 		$contacts['email'] = $data['email'];

	// 		unset($data['email']);

	// 	}

	// 	if (isset($data['Mobile_number'])) {

	// 		$client['phonenumber'] = $data['Mobile_number'];

	// 		unset($data['Mobile_number']);

	// 	}

	// 	if (isset($data['account_group'])) {

	// 		$client['ActGroupID'] = $data['account_group'];

	// 		unset($data['account_group']);

	// 	}

	// 	if (isset($data['vat'])) {

	// 		$client['vat'] = $data['vat'];

	// 		unset($data['vat']);

	// 	}



	// 	if (isset($data['food_lic_n'])) {

	// 		$contacts['FLNO1'] = $data['food_lic_n'];

	// 		unset($data['food_lic_n']);

	// 	}

	// 	if (isset($data['opening_b'])) {

	// 		$accountbalances['BAL1'] = $data['opening_b'];

	// 		unset($data['opening_b']);

	// 	}



	// 	if (isset($data['Satrt_date'])) {

	// 		$Satrt_date = to_sql_date($data['Satrt_date']);

	// 		$client['StartDate'] = $Satrt_date." ".date('H:i:s');

	// 		unset($data['Satrt_date']);

	// 	}



	// 	if (isset($data['gst_type'])) {

	// 		$client['gsttype'] = $data['gst_type'];

	// 		unset($data['gst_type']);

	// 	}



	// 	if (isset($data['pan'])) {

	// 		$contacts['Pan'] = $data['pan'];

	// 		unset($data['pan']);

	// 	}

	// 	if (isset($data['adhaar'])) {

	// 		$contacts['Aadhaarno'] = $data['adhaar'];

	// 		unset($data['adhaar']);

	// 	}



	// 	$contacts['datecreated'] = date('Y-m-d H:i:s');



	// 	if (is_staff_logged_in()) {

	// 		$client['addedfrom'] = $this->session->userdata('username');

	// 	}

	// 	/* echo "<pre>";

	// 		print_r($client);

	// 		print_r($contacts);

	// 		print_r($accountbalances);

	// 	die;*/



	// 	if(isset($client_id) && $client_id > 0){

	// 		$userid = $client_id;

	// 		} else {

	// 		$this->db->insert(db_prefix() . 'clients', $client);



	// 		$userid = $this->db->insert_id(); 



	// 		if ($userid) {

	//             $this->db->insert(db_prefix() . 'contacts', $contacts);

	//             $this->db->insert(db_prefix() . 'accountbalances', $accountbalances);

	// 		}

	// 	}

	// 	return $userid;

	// }



	/**

		* { update vendor }

		*

		* @param      <type>   $data            The data

		* @param      <type>   $id              The identifier

		* @param      boolean  $client_request  The client request

		*

		* @return     boolean 

	*/

	// public function update_vendor($data, $id, $client_request = false)

	// {

	// 	$UserID = $this->session->userdata('username');

	// 	if (isset($data['company'])) {

	// 		$client['company'] = $data['company'];

	// 		unset($data['company']);

	// 	}



	// 	if (isset($data['account_group'])) {

	// 		$client['SubActGroupID'] = $data['account_group'];

	// 		unset($data['account_group']);

	// 	}



	// 	if (isset($data['address'])) {

	// 		$client['address'] = $data['address'];

	// 		unset($data['address']);

	// 	}



	// 	if (isset($data['state'])) {

	// 		$client['state'] = $data['state'];

	// 		unset($data['state']);

	// 	}

	// 	if (isset($data['city'])) {

	// 		$client['city'] = $data['city'];

	// 		unset($data['city']);

	// 	}

	// 	if (isset($data['phonenumber'])) {

	// 		$client['altphonenumber'] = $data['phonenumber'];

	// 		unset($data['phonenumber']);

	// 	}

	// 	if (isset($data['address2'])) {

	// 		$client['Address3'] = $data['address2'];

	// 		unset($data['address2']);

	// 	}

	// 	if (isset($data['zip'])) {

	// 		$client['zip'] = $data['zip'];

	// 		unset($data['zip']);

	// 	}



	// 	if (isset($data['email'])) {

	// 		$contacts['email'] = $data['email'];

	// 		unset($data['email']);

	// 	}

	// 	if (isset($data['Mobile_number'])) {

	// 		$client['phonenumber'] = $data['Mobile_number'];

	// 		unset($data['Mobile_number']);

	// 	}



	// 	if (isset($data['vat'])) {

	// 		if($data['vat'] == ''){

	// 			$client['vat'] = NULL;

	// 			$client['gsttype'] = 2;

	// 			}else{

	// 			$client['vat'] = $data['vat'];

	// 			$client['gsttype'] = 1;

	// 		}



	// 		unset($data['vat']);

	// 	}



	// 	if (isset($data['food_lic_n'])) {

	// 		$contacts['FLNO1'] = $data['food_lic_n'];

	// 		unset($data['food_lic_n']);

	// 	}

	// 	if (isset($data['opening_b'])) {

	// 		$accountbalances['BAL1'] = $data['opening_b'];

	// 		$accountbalances['UserID2'] = $UserID;

	// 		$accountbalances['Lupdate'] = date('Y-m-d H:i:s');

	// 		unset($data['opening_b']);

	// 	}



	// 	if (isset($data['Satrt_date'])) {

	// 		$Satrt_date = to_sql_date($data['Satrt_date']);

	// 		$client['StartDate'] = $Satrt_date.' '.date('H:i:s');

	// 		unset($data['Satrt_date']);

	// 	}







	// 	if (isset($data['pan'])) {

	// 		$contacts['Pan'] = $data['pan'];

	// 		unset($data['pan']);

	// 	}

	// 	if (isset($data['adhaar'])) {

	// 		$contacts['Aadhaarno'] = $data['adhaar'];

	// 		unset($data['adhaar']);

	// 	}

	// 	$selected_company = $this->session->userdata('root_company');

	// 	$AccountID = $data['userid'];

	// 	$client['UserID2'] = $UserID;

	// 	$client['Lupdate'] = date('Y-m-d H:i:s');

	// 	/*echo "<pre>";

	// 		echo $AccountID;

	// 		echo $selected_company;

	// 		print_r($client);

	// 		print_r($contacts);

	// 		print_r($accountbalances);

	// 	die;*/

	// 	$this->db->where('PlantID', $selected_company);

	// 	$this->db->where('AccountID', $AccountID);

	// 	$this->db->update(db_prefix() . 'clients', $client);



	// 	$affectedRows++;



	//     $this->db->where('PlantID', $selected_company);

	//     $this->db->where('AccountID', $AccountID);

	//     $this->db->update(db_prefix() . 'contacts', $contacts);



	//     $year = $_SESSION['finacial_year'];

	//     $staff_user_id = $this->session->userdata('staff_user_id');

	//     if($staff_user_id == "3"){

	//         $this->db->where('PlantID', $selected_company);

	//         $this->db->where('AccountID', $AccountID);

	//         $this->db->where('FY', $year);

	//         $this->db->update(db_prefix() . 'accountbalances', $accountbalances);

	// 	}

	// 	if ($affectedRows > 0) {

	// 		hooks()->do_action('after_client_updated', $id);

	// 		return true;

	// 	}



	// 	return false;

	// }



	/**

		* { check zero columns }

		*

		* @param      <type>  $data   The data

		*

		* @return     array  

	*/

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



	/**

		* Gets the vendor admins.

		*

		* @param      <type>  $id     The identifier

		*

		* @return     <type>  The vendor admins.

	*/

	public function get_vendor_admins($id)
	{

		$this->db->where('vendor_id', $id);



		return $this->db->get(db_prefix() . 'pur_vendor_admin')->result_array();

	}





	/**

		* { assign vendor admins }

		*

		* @param      <type>   $data   The data

		* @param      <type>   $id     The identifier

		*

		* @return     boolean 

	*/

	public function assign_vendor_admins($data, $id)
	{

		$affectedRows = 0;



		if (count($data) == 0) {

			$this->db->where('vendor_id', $id);

			$this->db->delete(db_prefix() . 'pur_vendor_admin');

			if ($this->db->affected_rows() > 0) {

				$affectedRows++;

			}

		} else {

			$current_admins = $this->get_vendor_admins($id);

			$current_admins_ids = [];

			foreach ($current_admins as $c_admin) {

				array_push($current_admins_ids, $c_admin['staff_id']);

			}

			foreach ($current_admins_ids as $c_admin_id) {

				if (!in_array($c_admin_id, $data['customer_admins'])) {

					$this->db->where('staff_id', $c_admin_id);

					$this->db->where('vendor_id', $id);

					$this->db->delete(db_prefix() . 'pur_vendor_admin');

					if ($this->db->affected_rows() > 0) {

						$affectedRows++;

					}

				}

			}

			foreach ($data['customer_admins'] as $n_admin_id) {

				if (
					total_rows(db_prefix() . 'pur_vendor_admin', [

						'vendor_id' => $id,

						'staff_id' => $n_admin_id,

					]) == 0
				) {

					$this->db->insert(db_prefix() . 'pur_vendor_admin', [

						'vendor_id' => $id,

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

		* { delete vendor }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean  

	*/

	public function delete_vendor($id)
	{

		$affectedRows = 0;



		hooks()->do_action('before_client_deleted', $id);



		$last_activity = get_last_system_activity_id();

		$company = get_company_name($id);



		$this->db->where('userid', $id);

		$this->db->delete(db_prefix() . 'pur_vendor');

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

			// Delete all user contacts

			$this->db->where('userid', $id);

			$contacts = $this->db->get(db_prefix() . 'pur_contacts')->result_array();

			foreach ($contacts as $contact) {

				$this->delete_contact($contact['id']);

			}



			$this->db->where('relid', $id);

			$this->db->where('fieldto', 'vendor');

			$this->db->delete(db_prefix() . 'customfieldsvalues');



			$this->db->where('vendor_id', $id);

			$this->db->delete(db_prefix() . 'pur_vendor_admin');



			$this->db->where('rel_id', $id);

			$this->db->where('rel_type', 'pur_vendor');

			$this->db->delete(db_prefix() . 'files');

			if ($this->db->affected_rows() > 0) {

				$affectedRows++;

			}



			if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_vendor/' . $id)) {

				delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_vendor/' . $id);

			}



			$this->db->where('rel_type', 'pur_vendor');

			$this->db->where('rel_id', $id);

			$this->db->delete(db_prefix() . 'notes');

		}

		if ($affectedRows > 0) {

			hooks()->do_action('after_client_deleted', $id);



			return true;

		}



		return false;

	}



	/**

		* Adds a contact.

		*

		* @param      <type>   $data                The data

		* @param      <type>   $customer_id         The customer identifier

		* @param      boolean  $not_manual_request  Not manual request

		*

		* @return     boolean  or contact id

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





		if (isset($data['is_primary'])) {

			$data['is_primary'] = 1;

			$this->db->where('userid', $customer_id);

			$this->db->update(db_prefix() . 'pur_contacts', [

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



		$this->db->insert(db_prefix() . 'pur_contacts', $data);

		$contact_id = $this->db->insert_id();



		if ($contact_id) {

			if (isset($custom_fields)) {

				handle_custom_fields_post($contact_id, $custom_fields);

			}



			if ($not_manual_request == true) {

				// update all email notifications to 0

				$this->db->where('id', $contact_id);

				$this->db->update(db_prefix() . 'pur_contacts', [

					'invoice_emails' => 0,

					'estimate_emails' => 0,

					'credit_note_emails' => 0,

					'contract_emails' => 0,

					'task_emails' => 0,

					'project_emails' => 0,

					'ticket_emails' => 0,

				]);

			}





			hooks()->do_action('contact_created', $contact_id);



			return $contact_id;

		}



		return false;

	}



	/**

		* { update contact }

		*

		* @param      <type>   $data            The data

		* @param      <type>   $id              The identifier

		* @param      boolean  $client_request  The client request

		*

		* @return     boolean 

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

		$this->db->update(db_prefix() . 'pur_contacts', $data);



		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

			if (isset($data['is_primary']) && $data['is_primary'] == 1) {

				$this->db->where('userid', $contact->userid);

				$this->db->where('id !=', $id);

				$this->db->update(db_prefix() . 'pur_contacts', [

					'is_primary' => 0,

				]);

			}

		}





		if ($affectedRows > 0) {

			return true;

		}



		return false;

	}



	/**

		* { delete contact }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean  

	*/

	public function delete_contact($id)
	{

		hooks()->do_action('before_delete_contact', $id);



		$this->db->where('id', $id);

		$result = $this->db->get(db_prefix() . 'pur_contacts')->row();

		$customer_id = $result->userid;



		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_contacts');



		if ($this->db->affected_rows() > 0) {



			hooks()->do_action('contact_deleted', $id, $result);



			return true;

		}



		return false;

	}



	/**

		* Gets the approval setting.

		*

		* @param      string  $id     The identifier

		*

		* @return     <type>  The approval setting.

	*/

	public function get_approval_setting($id = '')
	{

		if (is_numeric($id)) {

			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'pur_approval_setting')->row();

		}

		return $this->db->get(db_prefix() . 'pur_approval_setting')->result_array();

	}



	/**

		* Adds an approval setting.

		*

		* @param      <type>   $data   The data

		*

		* @return     boolean 

	*/

	public function add_approval_setting($data)
	{

		unset($data['approval_setting_id']);



		if (isset($data['approver'])) {

			$setting = [];

			foreach ($data['approver'] as $key => $value) {

				$node = [];

				$node['approver'] = $data['approver'][$key];

				$node['staff'] = $data['staff'][$key];

				$node['action'] = $data['action'][$key];



				$setting[] = $node;

			}

			unset($data['approver']);

			unset($data['staff']);

			unset($data['action']);

		}

		$data['setting'] = json_encode($setting);



		$this->db->insert(db_prefix() . 'pur_approval_setting', $data);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {

			return true;

		}

		return false;

	}



	/**

		* { edit approval setting }

		*

		* @param      <type>   $id     The identifier

		* @param      <type>   $data   The data

		*

		* @return     boolean  

	*/

	public function edit_approval_setting($id, $data)
	{

		unset($data['approval_setting_id']);



		if (isset($data['approver'])) {

			$setting = [];

			foreach ($data['approver'] as $key => $value) {

				$node = [];

				$node['approver'] = $data['approver'][$key];

				$node['staff'] = $data['staff'][$key];

				$node['action'] = $data['action'][$key];



				$setting[] = $node;

			}

			unset($data['approver']);

			unset($data['staff']);

			unset($data['action']);

		}

		$data['setting'] = json_encode($setting);



		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_approval_setting', $data);



		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* { delete approval setting }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean   

	*/

	public function delete_approval_setting($id)
	{

		if (is_numeric($id)) {

			$this->db->where('id', $id);

			$this->db->delete(db_prefix() . 'pur_approval_setting');



			if ($this->db->affected_rows() > 0) {

				return true;

			}

		}

		return false;

	}



	/**

		* Gets the items.

		*

		* @return     <array>  The items.

	*/

	public function get_items()
	{

		$this->db->select('*');
		$this->db->from(db_prefix() . 'items');
		$this->db->where('IsActive', 'Y');

		return $this->db->get()->result_array();
	}




	public function get_items_code()
	{

		$selected_company = $this->session->userdata('root_company');

		//   $year = $_SESSION['finacial_year'];

		// return $this->db->query('select id as id, CONCAT(ItemID," - ",description) as label,ItemID from ' . db_prefix() . '	items where PlantID = ' . $selected_company)->result_array();

	}



	public function GetVendorWiseItems($AccountID)
	{

		$selected_company = $this->session->userdata('root_company');

		//   $year = $_SESSION['finacial_year'];

		return $this->db->query('select tblitems.id as id, CONCAT(tblitems.ItemID," - ",tblitems.description) as label,tblitems.ItemID from ' . db_prefix() . 'VendorWiseItems

			Inner JOIN tblitems ON tblitems.ItemID = tblVendorWiseItems.ItemID AND tblitems.PlantID = ' . $selected_company . '

			where tblVendorWiseItems.AccountID = "' . $AccountID . '" AND tblVendorWiseItems.status = "Y" AND PlantID = ' . $selected_company)->result_array();

	}



	public function GetVendorWiseItemsEdit($AccountID)
	{

		$selected_company = $this->session->userdata('root_company');

		//   $year = $_SESSION['finacial_year'];

		return $this->db->query('select tblitems.id as id, CONCAT(tblitems.ItemID," - ",tblitems.description) as label,tblitems.ItemID from ' . db_prefix() . 'VendorWiseItems

			Inner JOIN tblitems ON tblitems.ItemID = tblVendorWiseItems.ItemID AND tblitems.PlantID = ' . $selected_company . '

			where tblVendorWiseItems.AccountID = "' . $AccountID . '" AND PlantID = ' . $selected_company)->result_array();

	}

	public function GetVendorWiseItemsEditReturn($AccountID)
	{

		$selected_company = $this->session->userdata('root_company');

		//   $year = $_SESSION['finacial_year'];

		return $this->db->query('select tblitems.ItemID as id, CONCAT(tblitems.ItemID," - ",tblitems.description) as label,tblitems.ItemID from ' . db_prefix() . 'VendorWiseItems

			Inner JOIN tblitems ON tblitems.ItemID = tblVendorWiseItems.ItemID AND tblitems.PlantID = ' . $selected_company . '

			where tblVendorWiseItems.AccountID = "' . $AccountID . '" AND PlantID = ' . $selected_company)->result_array();

	}



	public function get_acc_head()
	{

		$selected_company = $this->session->userdata('root_company');

		//   $year = $_SESSION['finacial_year'];

		return $this->db->query('select AccountID as id, CONCAT(AccountID," - ",company) as label,AccountID from ' . db_prefix() . 'clients where PlantID = ' . $selected_company . ' AND tblclients.tax IS NOT NULL ORDER BY tblclients.company ASC')->result_array();

	}

	public function account_change_by_AccountID($code)
	{





		$sql = "SELECT tblclients.*,tblclients.hsn_code AS hsn,tbltaxes.taxrate AS Gst FROM `tblclients`

			left JOIN tbltaxes ON tbltaxes.id=tblclients.tax

			WHERE `tblclients`.`AccountID` = '" . $code . "'";

		$query = $this->db->query($sql);

		return $query->row();



	}

	public function get_items_code_purReturn()
	{

		$selected_company = $this->session->userdata('root_company');

		//   $year = $_SESSION['finacial_year'];

		return $this->db->query('select ItemID as ItemID, CONCAT(ItemID," - ",description) as label,ItemID from ' . db_prefix() . 'items where PlantID = ' . $selected_company)->result_array();

	}

	public function get_items_for_purchRtn()
	{

		$selected_company = $this->session->userdata('root_company');

		return $this->db->query('select ItemID as id, CONCAT(ItemID," - " ,description) as label from ' . db_prefix() . 'items where PlantID = ' . $selected_company)->result_array();

	}

	/**

		* Gets the items by vendor.

		*

		* @return     <array>  The items.

	*/

	public function get_items_by_vendor($vendor)
	{

		return $this->db->query('select id as id, CONCAT(commodity_code," - " ,description) as label from ' . db_prefix() . 'items where id IN ( select items from ' . db_prefix() . 'pur_vendor_items where vendor = ' . $vendor . ' )')->result_array();

	}

	public function get_items_by_vendor_data($item, $vendor)
	{

		return $this->db->query('select id as id, CONCAT(commodity_code," - " ,description) as label from ' . db_prefix() . 'items where id IN ( select items from ' . db_prefix() . 'pur_vendor_items where vendor = "' . $vendor . '" and items =' . $item . ' )')->result_array();

	}

	public function items_purchaseid_check($item, $vendor)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		//$item_details =  $this->db->get_where('tblitems',array('PlantID'=>$selected_company,'id'=>$item))->row();



		$this->db->select('tblhistory.*,tblpurchasemaster.Invoiceno,tblpurchasemaster.Invoicedate');

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'purchasemaster', db_prefix() . 'purchasemaster.PurchID = ' . db_prefix() . 'history.OrderID', 'left');

		$this->db->where(db_prefix() . 'history.TType', 'P');

		$this->db->where(db_prefix() . 'history.TType2', 'Purchase');

		$this->db->where(db_prefix() . 'history.ItemID', $item);

		$this->db->where(db_prefix() . 'history.AccountID', $vendor);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		return $this->db->get()->result_array();

	}

	public function items_vendor_check_tcs($vendor)
	{

		//   return $this->db->get_where('clients',array('userid' =>$id))->row();

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'clients');

		$this->db->join(db_prefix() . 'contacts', db_prefix() . 'contacts.AccountID = ' . db_prefix() . 'clients.AccountID', 'left');

		$this->db->where(db_prefix() . 'clients.userid', $vendor);

		$this->db->where(db_prefix() . 'contacts.PlantID', $selected_company);

		return $this->db->get()->row();

		//   echo $this->db->last_query();die;

	}

	/**

		* Gets the items by identifier.

		*

		* @param      <type>  $id     The identifier

		*

		* @return     <row>  The items by identifier.

	*/

	public function get_items_by_id($id)
	{

		$this->db->where('id', $id);

		return $this->db->get(db_prefix() . 'items')->row();

	}



	/**

		* Gets the units by identifier.

		*

		* @param      <type>  $id     The identifier

		*

		* @return     <row>  The units by identifier.

	*/

	public function get_units_by_id($id)
	{

		$this->db->where('unit_type_id', $id);

		return $this->db->get(db_prefix() . 'ware_unit_type')->row();

	}



	/**

		* Gets the units.

		*

		* @return     <array>  The list units.

	*/

	public function get_units()
	{

		return $this->db->query('select unit_type_id as id, unit_name as label from ' . db_prefix() . 'ware_unit_type')->result_array();

	}



	/**

		* { items change event}

		*

		* @param      <type>  $code   The code

		*

		* @return     <row>  ( item )

	*/

	public function get_accounts_list()
	{

		$selected_company = $this->session->userdata('root_company');

		$ss = 'SELECT *

			FROM tblclients WHERE PlantID =' . $selected_company . ' AND IsActive = 1 AND ActSubGroupID2 NOT IN("1000012")';

		$result_data = $this->db->query($ss)->result_array();

		return $result_data;

	}

	public function get_accounts_freightid($id = "")
	{

		$selected_company = $this->session->userdata('root_company');

		if (!empty($id)) {

			$ss = 'SELECT *

				FROM tblclients  WHERE AccountID="' . $id . '" AND PlantID =' . $selected_company . ' AND IsActive = 1 AND ActSubGroupID2 NOT IN("1000012")';



			$result_data = $this->db->query($ss)->row();

			return $result_data;

		} else {

			$ss = 'SELECT *

				FROM tblclients WHERE AccountID="209" AND PlantID =' . $selected_company . ' AND IsActive = 1 AND ActSubGroupID2 NOT IN("1000012")';

			$result_data = $this->db->query($ss)->row();

			return $result_data;

		}

	}

	public function get_accounts_othertid($id = "")
	{

		$selected_company = $this->session->userdata('root_company');

		if ($selected_company == "1") {

			$OACT = "92";

		} else if ($selected_company == "2") {

			$OACT = "92";

		} else if ($selected_company == "3") {

			$OACT = "ME";

		}

		if (!empty($id)) {

			$ss = 'SELECT *

				FROM tblclients  WHERE AccountID="' . $id . '" AND PlantID =' . $selected_company . ' AND IsActive = 1 AND ActSubGroupID2 NOT IN("30000004","10022003","10022004","10022005","1002504","1002503","1002506","30000006","30000007","30001002","50003002","60001114")';



			$result_data = $this->db->query($ss)->row();

			return $result_data;

		} else {

			$ss = 'SELECT *

				FROM tblclients WHERE AccountID ="' . $OACT . '" AND PlantID =' . $selected_company . ' AND IsActive = 1 AND ActSubGroupID2 NOT IN("30000004","10022003","10022004","10022005","1002504","1002503","1002506","30000006","30000007","30001002","50003002","61114")';



			$result_data = $this->db->query($ss)->row();

			// echo $this->db->last_query();die;

			return $result_data;

		}

	}

	public function items_change($code)
	{

		// $this->db->where('id',$code);

		// $rs = $this->db->get(db_prefix().'items')->row();



		$this->db->select();

		$this->db->from(db_prefix() . 'items');

		$this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id = ' . db_prefix() . 'items.tax', 'left');

		$this->db->where(db_prefix() . 'items.id', $code);

		//   $this->db->where(db_prefix() .'contacts.PlantID', $selected_company);

		$rs = $this->db->get()->row();



		$sql = "SELECT * FROM `tblitems` LEFT JOIN `tbltaxes` ON `tbltaxes`.`id` = `tblitems`.`tax` 

			LEFT JOIN `tblitemsSubGroup2` ON `tblitemsSubGroup2`.`id` = `tblitems`.`SubGrpID1` LEFT JOIN `tblitems_main_groups` ON `tblitems_main_groups`.`id` = `tblitemsSubGroup2`.`main_DivisionID` WHERE `tblitems`.`id` = '" . $code . "'";

		$query = $this->db->query($sql);



		$data = $query->row();

		$data->old_rate = $this->get_p_order_detail_old_item($data->ItemID);

		$data->stock_avl = $this->GetItemStock($data->ItemID);

		return $data;

		//   echo $this->db->last_query();die;

		// return $rs;

		$this->db->where('unit_type_id', $rs->unit_id);

		$unit = $this->db->get(db_prefix() . 'ware_unit_type')->row();



		if ($unit) {

			$rs->unit = $unit->unit_name;

		} else {

			$rs->unit = '';

		}



		if (get_status_modules_pur('warehouse') == true) {

			$this->db->where('commodity_id', $code);

			$commo = $this->db->get(db_prefix() . 'inventory_manage')->result_array();

			$rs->inventory = 0;

			if (count($commo) > 0) {

				foreach ($commo as $co) {

					$rs->inventory += $co['inventory_number'];

				}

			}

		} else {

			$rs->inventory = 0;

		}



		return $rs;

	}



	/**

		* Gets the purchase request.

		*

		* @param      string  $id     The identifier

		*

		* @return     <row or array>  The purchase request.

	*/

	public function get_purchase_request($id = '')
	{

		if ($id == '') {

			return $this->db->get(db_prefix() . 'pur_request')->result_array();

		} else {

			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'pur_request')->row();

		}

	}



	/**

		* Gets the pur request detail.

		*

		* @param      <int>  $pur_request  The pur request

		*

		* @return     <array>  The pur request detail.

	*/

	public function get_pur_request_detail($pur_request)
	{

		$this->db->where('pur_request', $pur_request);

		return $this->db->get(db_prefix() . 'pur_request_detail')->result_array();

	}



	/**

		* Gets the pur request detail in estimate.

		*

		* @param      <int>  $pur_request  The pur request

		*

		* @return     <array>  The pur request detail in estimate.

	*/

	public function get_pur_request_detail_in_estimate($pur_request)
	{

		$this->db->where('pur_request', $pur_request);

		$this->db->select('ItemID');

		$this->db->select('unit_id');

		$this->db->select('unit_price');

		$this->db->select('quantity');

		$this->db->select('into_money');

		return $this->db->get(db_prefix() . 'pur_request_detail')->result_array();

	}



	/**

		* Gets the pur estimate detail in order.

		*

		* @param      <int>  $pur_estimate  The pur estimate

		*

		* @return     <array>  The pur estimate detail in order.

	*/

	public function get_pur_estimate_detail_in_order($pur_estimate)
	{

		$this->db->where('pur_estimate', $pur_estimate);

		$this->db->select('ItemID');

		$this->db->select('unit_id');

		$this->db->select('unit_price');

		$this->db->select('quantity');

		$this->db->select('into_money');

		$this->db->select('tax');

		$this->db->select('total');

		$this->db->select('total_money');

		$this->db->select('discount_money');

		$this->db->select('discount_%');

		return $this->db->get(db_prefix() . 'pur_estimate_detail')->result_array();

	}



	/**

		* Gets the pur estimate detail.

		*

		* @param      <int>  $pur_request  The pur request

		*

		* @return     <array>  The pur estimate detail.

	*/

	public function get_pur_estimate_detail($pur_request)
	{

		$this->db->where('pur_estimate', $pur_request);

		return $this->db->get(db_prefix() . 'pur_estimate_detail')->result_array();

	}



	/**

		* Gets the pur order detail.

		*

		* @param      <int>  $pur_request  The pur request

		*

		* @return     <array>  The pur order detail.

	*/

	public function get_pur_order_detail($pur_request)
	{

		$this->db->where('pur_order', $pur_request);

		return $this->db->get(db_prefix() . 'pur_order_detail')->result_array();

	}



	public function get_p_order_detail($pur_request)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select(db_prefix() . 'items_sub_groups.main_DivisionID,' . db_prefix() . 'history.*,tblhistory.DiscPerc as Disc,' . db_prefix() . 'items.*,' . db_prefix() . 'items_main_groups.name');

		// $this->db->select( db_prefix() . 'clients.company,'.db_prefix() . 'clients.userid,'.db_prefix() . 'clients.AccountID,');

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID', 'left');

		$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'left');

		$this->db->join(db_prefix() . 'items_main_groups', db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'items_sub_groups.main_DivisionID', 'left');

		$this->db->where(db_prefix() . 'history.OrderID', $pur_request);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		$data = $this->db->get()->result_array();

		foreach ($data as $key => $value) {

			$data[$key]['sub_total'] = $value['OrderAmt'] + $value['cgstamt'] + $value['sgstamt'] + $value['igstamt'];

			$data[$key]['total'] = $value['OrderAmt'] + $value['cgstamt'] + $value['sgstamt'] + $value['igstamt'] + $value['DiscAmt'];

			$data[$key]['stock_avl'] = $this->GetItemStock($value['ItemID']);

		}

		return $data;



	}



	/**

		* Adds a pur request.

		*

		* @param      <array>   $data   The data

		*

		* @return     boolean  

	*/

	public function add_pur_request($data)
	{



		$data['request_date'] = date('Y-m-d H:i:s');

		$check_appr = $this->get_approve_setting('pur_request');

		$data['status'] = 1;

		if ($check_appr && $check_appr != false) {

			$data['status'] = 1;

		} else {

			$data['status'] = 2;

		}



		if (isset($data['from_items'])) {

			$data['from_items'] = 1;

		} else {

			if ($data['status'] != 2) {

				$data['from_items'] = 0;

			} else {

				$data['from_items'] = 1;

			}



		}



		$dpm_name = department_pur_request_name($data['department']);

		$prefix = get_purchase_option('pur_order_prefix');



		$this->db->where('pur_rq_code', $data['pur_rq_code']);

		$check_exist_number = $this->db->get(db_prefix() . 'pur_request')->row();



		while ($check_exist_number) {

			$data['number'] = $data['number'] + 1;

			$data['pur_rq_code'] = $prefix . '-' . str_pad($data['number'], 5, '0', STR_PAD_LEFT) . '-' . date('M-Y') . '-' . $dpm_name;

			$this->db->where('pur_rq_code', $data['pur_rq_code']);

			$check_exist_number = $this->db->get(db_prefix() . 'pur_request')->row();

		}



		$data['hash'] = app_generate_hash();



		if (isset($data['request_detail'])) {

			$request_detail = json_decode($data['request_detail']);

			unset($data['request_detail']);

			$rq_detail = [];

			$row = [];

			$rq_val = [];

			$header = [];



			if ($data['from_items'] == 1) {

				$header[] = 'ItemID';

			} else {

				$header[] = 'item_text';

			}



			$header[] = 'unit_id';

			$header[] = 'unit_price';

			$header[] = 'quantity';

			$header[] = 'into_money';

			$header[] = 'inventory_quantity';



			foreach ($request_detail as $key => $value) {



				if ($value[0] != '') {

					$rq_detail[] = array_combine($header, $value);

				}

			}

		}



		$this->db->insert(db_prefix() . 'pur_request', $data);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {



			// Update next purchase order number in settings

			$next_number = $data['number'] + 1;

			$this->db->where('option_name', 'next_pr_number');

			$this->db->update(db_prefix() . 'purchase_option', ['option_val' => $next_number,]);



			foreach ($rq_detail as $key => $rqd) {

				$rq_detail[$key]['pur_request'] = $insert_id;

				if ($data['status'] == 2) {

					$item_data['description'] = $rqd['ItemID'];

					$item_data['purchase_price'] = $rqd['unit_price'];

					$item_data['unit_id'] = $rqd['unit_id'];

					$item_data['rate'] = '';

					$item_data['sku_code'] = '';

					$item_data['commodity_barcode'] = $this->generate_commodity_barcode();

					$item_data['commodity_code'] = $this->generate_commodity_barcode();

					$item_id = $this->add_commodity_one_item($item_data);

					if ($item_id) {

						$rq_detail[$key]['ItemID'] = $item_id;

					}



				}

			}

			$this->db->insert_batch(db_prefix() . 'pur_request_detail', $rq_detail);

			return $insert_id;

		}

		return false;

	}



	/**

		* { update pur request }

		*

		* @param      <array>   $data   The data

		* @param      <int>   $id     The identifier

		*

		* @return     boolean   

	*/

	public function update_pur_request($data, $id)
	{

		$affectedRows = 0;

		$purq = $this->get_purchase_request($id);



		if (isset($data['request_detail'])) {

			$request_detail = json_decode($data['request_detail']);

			unset($data['request_detail']);

			$rq_detail = [];

			$row = [];

			$rq_val = [];

			$header = [];

			$header[] = 'prd_id';

			$header[] = 'pur_request';

			if ($purq) {

				if ($purq->from_items == 0) {

					$header[] = 'item_text';

				} else {

					$header[] = 'ItemID';

				}

			}



			$header[] = 'unit_id';

			$header[] = 'unit_price';

			$header[] = 'quantity';

			$header[] = 'into_money';

			$header[] = 'inventory_quantity';



			foreach ($request_detail as $key => $values) {



				if ($values[2] != '') {

					$rq_detail[] = array_combine($header, $values);

				}

			}

		}



		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_request', $data);

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		$row = [];

		$row['update'] = [];

		$row['insert'] = [];

		$row['delete'] = [];

		foreach ($rq_detail as $key => $value) {

			if ($value['prd_id'] != '') {

				$row['delete'][] = $value['prd_id'];

				$row['update'][] = $value;

			} else {

				unset($value['prd_id']);

				$value['pur_request'] = $id;

				$row['insert'][] = $value;

			}

		}



		if (count($row['delete']) != 0) {

			$row['delete'] = implode(",", $row['delete']);

			$this->db->where('prd_id NOT IN (' . $row['delete'] . ') and pur_request =' . $id);

			$this->db->delete(db_prefix() . 'pur_request_detail');

			if ($this->db->affected_rows() > 0) {

				$affectedRows++;

			}

		}

		if (count($row['insert']) != 0) {

			$this->db->insert_batch(db_prefix() . 'pur_request_detail', $row['insert']);

			if ($this->db->affected_rows() > 0) {

				$affectedRows++;

			}

		}

		if (count($row['update']) != 0) {

			$this->db->update_batch(db_prefix() . 'pur_request_detail', $row['update'], 'prd_id');

			if ($this->db->affected_rows() > 0) {

				$affectedRows++;

			}

		}





		if ($affectedRows > 0) {

			return true;

		}

		return false;

	}



	/**

		* { delete pur request }

		*

		* @param      <int>   $id     The identifier

		*

		* @return     boolean  

	*/

	public function delete_pur_request($id)
	{

		$affectedRows = 0;

		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_request');

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		$this->db->where('pur_request', $id);

		$this->db->delete(db_prefix() . 'pur_request_detail');

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		if ($affectedRows > 0) {

			return true;

		}

		return false;

	}



	/**

		* { change status pur request }

		*

		* @param      <type>   $status  The status

		* @param      <type>   $id      The identifier

		*

		* @return     boolean 

	*/

	public function change_status_pur_request($status, $id)
	{

		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_request', ['status' => $status]);

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* Gets the pur request by status.

		*

		* @param      <type>  $status  The status

		*

		* @return     <array>  The pur request by status.

	*/

	public function get_pur_request_by_status($status)
	{

		$this->db->where('status', $status);

		return $this->db->get(db_prefix() . 'pur_request')->result_array();

	}



	/**

		* { function_description }

		*

		* @param      <type>  $data   The data

		*

		* @return     <array> data

	*/

	private function map_shipping_columns($data)
	{

		if (!isset($data['include_shipping'])) {

			foreach ($this->shipping_fields as $_s_field) {

				if (isset($data[$_s_field])) {

					$data[$_s_field] = null;

				}

			}

			$data['show_shipping_on_estimate'] = 1;

			$data['include_shipping'] = 0;

		} else {

			$data['include_shipping'] = 1;

			// set by default for the next time to be checked

			if (isset($data['show_shipping_on_estimate']) && ($data['show_shipping_on_estimate'] == 1 || $data['show_shipping_on_estimate'] == 'on')) {

				$data['show_shipping_on_estimate'] = 1;

			} else {

				$data['show_shipping_on_estimate'] = 0;

			}

		}



		return $data;

	}



	/**

		* Gets the estimate.

		*

		* @param      string  $id     The identifier

		* @param      array   $where  The where

		*

		* @return     <row , array>  The estimate, list estimate.

	*/

	public function get_estimate($id = '', $where = [])
	{

		$this->db->select('*,' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'pur_estimates.id as id, ' . db_prefix() . 'currencies.name as currency_name');

		$this->db->from(db_prefix() . 'pur_estimates');

		$this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'pur_estimates.currency', 'left');

		$this->db->where($where);

		if (is_numeric($id)) {

			$this->db->where(db_prefix() . 'pur_estimates.id', $id);

			$estimate = $this->db->get()->row();

			if ($estimate) {



				$estimate->visible_attachments_to_customer_found = false;



				$estimate->items = get_items_by_type('pur_estimate', $id);



				if ($estimate->pur_request != 0) {



					$estimate->pur_request = $this->get_purchase_request($estimate->pur_request);

				} else {

					$estimate->pur_request = '';

				}



				$estimate->vendor = $this->get_vendor($estimate->vendor);

				if (!$estimate->vendor) {

					$estimate->vendor = new stdClass();

					$estimate->vendor->company = $estimate->deleted_customer_name;

				}

			}



			return $estimate;

		}

		$this->db->order_by('number,YEAR(date)', 'desc');



		return $this->db->get()->result_array();

	}



	/**

		* Gets the pur order.

		*

		* @param      <int>  $id     The identifier

		*

		* @return     <row>  The pur order.

	*/

	public function get_pur_order($id)
	{

		$this->db->where('id', $id);

		return $this->db->get(db_prefix() . 'pur_orders')->row();

	}





	/**

		* Adds an estimate.

		*

		* @param      <type>   $data   The data

		*

		* @return     boolean  or in estimate

	*/

	public function add_estimate($data)
	{

		$check_appr = $this->get_approve_setting('pur_quotation');

		$data['status'] = 1;

		if ($check_appr && $check_appr != false) {

			$data['status'] = 1;

		} else {

			$data['status'] = 2;

		}

		$data['date'] = to_sql_date($data['date']);

		$data['expirydate'] = to_sql_date($data['expirydate']);



		$data['datecreated'] = date('Y-m-d H:i:s');



		$data['addedfrom'] = get_staff_user_id();



		$data['prefix'] = get_option('estimate_prefix');



		$data['number_format'] = get_option('estimate_number_format');



		$this->db->where('prefix', $data['prefix']);

		$this->db->where('number', $data['number']);

		$check_exist_number = $this->db->get(db_prefix() . 'pur_estimates')->row();



		while ($check_exist_number) {

			$data['number'] = $data['number'] + 1;



			$this->db->where('prefix', $data['prefix']);

			$this->db->where('number', $data['number']);

			$check_exist_number = $this->db->get(db_prefix() . 'pur_estimates')->row();

		}



		$save_and_send = isset($data['save_and_send']);



		if (isset($data['custom_fields'])) {

			$custom_fields = $data['custom_fields'];

			unset($data['custom_fields']);

		}



		$data['hash'] = app_generate_hash();



		$data = $this->map_shipping_columns($data);



		if (isset($data['shipping_street'])) {

			$data['shipping_street'] = trim($data['shipping_street']);

			$data['shipping_street'] = nl2br($data['shipping_street']);

		}



		if (isset($data['dc_total'])) {

			$data['discount_total'] = reformat_currency_pur($data['dc_total']);

			unset($data['dc_total']);

		}



		if (isset($data['dc_percent'])) {

			$data['discount_percent'] = $data['dc_percent'];

			unset($data['dc_percent']);

		}



		if (isset($data['estimate_detail'])) {

			$estimate_detail = json_decode($data['estimate_detail']);

			unset($data['estimate_detail']);

			$es_detail = [];

			$row = [];

			$rq_val = [];

			$header = [];

			$header[] = 'ItemID';

			$header[] = 'unit_id';

			$header[] = 'unit_price';

			$header[] = 'quantity';

			$header[] = 'into_money';

			$header[] = 'tax';

			$header[] = 'total';

			$header[] = 'discount_%';

			$header[] = 'discount_money';

			$header[] = 'total_money';



			foreach ($estimate_detail as $key => $value) {



				if ($value[0] != '') {

					$es_detail[] = array_combine($header, $value);

				}

			}

		}





		$this->db->insert(db_prefix() . 'pur_estimates', $data);

		$insert_id = $this->db->insert_id();



		if ($insert_id) {

			$total = [];

			$total['total'] = 0;

			$total['total_tax'] = 0;

			$total['subtotal'] = 0;



			foreach ($es_detail as $key => $rqd) {

				$es_detail[$key]['pur_estimate'] = $insert_id;

				$total['total'] += $rqd['total_money'];

				$total['total_tax'] += ($rqd['total'] - $rqd['into_money']);

				$total['subtotal'] += $rqd['into_money'];

			}



			if ($data['discount_total'] > 0) {

				$total['total'] = $total['total'] - $data['discount_total'];

			}



			$this->db->insert_batch(db_prefix() . 'pur_estimate_detail', $es_detail);



			$this->db->where('id', $insert_id);

			$this->db->update(db_prefix() . 'pur_estimates', $total);



			if (isset($custom_fields)) {

				handle_custom_fields_post($insert_id, $custom_fields);

			}



			hooks()->do_action('after_estimate_added', $insert_id);



			return $insert_id;

		}



		return false;

	}



	/**

		* { update estimate }

		*

		* @param      <type>   $data   The data

		* @param      <type>   $id     The identifier

		*

		* @return     boolean  

	*/

	public function update_estimate($data, $id)
	{

		$data['date'] = to_sql_date($data['date']);

		$data['expirydate'] = to_sql_date($data['expirydate']);

		$affectedRows = 0;



		$data['number'] = trim($data['number']);



		$original_estimate = $this->get_estimate($id);



		$original_status = $original_estimate->status;



		$original_number = $original_estimate->number;



		$original_number_formatted = format_estimate_number($id);



		$data = $this->map_shipping_columns($data);



		unset($data['isedit']);



		if (isset($data['estimate_detail'])) {

			$estimate_detail = json_decode($data['estimate_detail']);

			unset($data['estimate_detail']);

			$es_detail = [];

			$row = [];

			$rq_val = [];

			$header = [];

			$header[] = 'id';

			$header[] = 'pur_estimate';

			$header[] = 'ItemID';

			$header[] = 'unit_id';

			$header[] = 'unit_price';

			$header[] = 'quantity';

			$header[] = 'into_money';

			$header[] = 'tax';

			$header[] = 'total';

			$header[] = 'discount_%';

			$header[] = 'discount_money';

			$header[] = 'total_money';



			foreach ($estimate_detail as $key => $value) {



				if ($value[2] != '') {

					$es_detail[] = array_combine($header, $value);

				}

			}

		}



		if (isset($data['dc_total'])) {

			$data['discount_total'] = reformat_currency_pur($data['dc_total']);

			unset($data['dc_total']);

		}



		if (isset($data['dc_percent'])) {

			$data['discount_percent'] = $data['dc_percent'];

			unset($data['dc_percent']);

		}



		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_estimates', $data);



		if ($this->db->affected_rows() > 0) {

			if ($original_status != $data['status']) {

				if ($data['status'] == 2) {

					$this->db->where('id', $id);

					$this->db->update(db_prefix() . 'pur_estimates', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);

				}

			}

			$affectedRows++;

		}







		$row = [];

		$row['update'] = [];

		$row['insert'] = [];

		$row['delete'] = [];

		$total = [];

		$total['total'] = 0;

		$total['total_tax'] = 0;

		$total['subtotal'] = 0;



		foreach ($es_detail as $key => $value) {

			if ($value['id'] != '') {

				$row['delete'][] = $value['id'];

				$row['update'][] = $value;

			} else {

				unset($value['id']);

				$value['pur_estimate'] = $id;

				$row['insert'][] = $value;

			}



			$total['total'] += $value['total_money'];

			$total['total_tax'] += ($value['total'] - $value['into_money']);

			$total['subtotal'] += $value['into_money'];



		}



		if ($data['discount_total'] > 0) {

			$total['total'] = $total['total'] - $data['discount_total'];

		}

		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_estimates', $total);

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		if (empty($row['delete'])) {

			$row['delete'] = ['0'];

		}

		$row['delete'] = implode(",", $row['delete']);

		$this->db->where('id NOT IN (' . $row['delete'] . ') and pur_estimate =' . $id);

		$this->db->delete(db_prefix() . 'pur_estimate_detail');

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		if (count($row['insert']) != 0) {

			$this->db->insert_batch(db_prefix() . 'pur_estimate_detail', $row['insert']);

			if ($this->db->affected_rows() > 0) {

				$affectedRows++;

			}

		}

		if (count($row['update']) != 0) {

			$this->db->update_batch(db_prefix() . 'pur_estimate_detail', $row['update'], 'id');

			if ($this->db->affected_rows() > 0) {

				$affectedRows++;

			}

		}





		if ($affectedRows > 0) {





			return true;

		}



		return false;

	}



	/**

		* Gets the estimate item.

		*

		* @param      <type>  $id     The identifier

		*

		* @return     <row>  The estimate item.

	*/

	public function get_estimate_item($id)
	{

		$this->db->where('id', $id);



		return $this->db->get(db_prefix() . 'itemable')->row();

	}



	/**

		* { delete estimate }

		*

		* @param      string   $id            The identifier

		* @param      boolean  $simpleDelete  The simple delete

		*

		* @return     boolean  ( description_of_the_return_value )

	*/

	public function delete_estimate($id, $simpleDelete = false)
	{





		hooks()->do_action('before_estimate_deleted', $id);



		$number = format_estimate_number($id);



		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_estimates');



		if ($this->db->affected_rows() > 0) {



			$this->db->where('pur_estimate', $id);

			$this->db->delete(db_prefix() . 'pur_estimate_detail');



			$this->db->where('relid IN (SELECT id from ' . db_prefix() . 'itemable WHERE rel_type="pur_estimate" AND rel_id="' . $id . '")');

			$this->db->where('fieldto', 'items');

			$this->db->delete(db_prefix() . 'customfieldsvalues');



			$this->db->where('rel_type', 'pur_estimate');

			$this->db->where('rel_id', $id);

			$this->db->delete(db_prefix() . 'taggables');



			$this->db->where('rel_id', $id);

			$this->db->where('rel_type', 'pur_estimate');

			$this->db->delete(db_prefix() . 'itemable');



			$this->db->where('rel_id', $id);

			$this->db->where('rel_type', 'pur_estimate');

			$this->db->delete(db_prefix() . 'item_tax');



			$this->db->where('rel_id', $id);

			$this->db->where('rel_type', 'pur_estimate');

			$this->db->delete(db_prefix() . 'sales_activity');



			return true;

		}



		return false;

	}



	/**

		* Gets the taxes.

		*

		* @return     <array>  The taxes.

	*/

	public function get_taxes()
	{

		return $this->db->query('select id, name as label, taxrate from ' . db_prefix() . 'taxes')->result_array();

	}



	/**

		* Gets the total tax.

		*

		* @param      <type>   $taxes  The taxes

		*

		* @return     integer  The total tax.

	*/

	public function get_total_tax($taxes)
	{

		$rs = 0;

		foreach ($taxes as $tax) {

			$this->db->where('id', $tax);

			$this->db->select('taxrate');

			$ta = $this->db->get(db_prefix() . 'taxes')->row();

			$rs += $ta->taxrate;

		}

		return $rs;

	}



	/**

		* { change status pur estimate }

		*

		* @param      <type>   $status  The status

		* @param      <type>   $id      The identifier

		*

		* @return     boolean   

	*/

	public function change_status_pur_estimate($status, $id)
	{

		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_estimates', ['status' => $status]);

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* { change status pur order }

		*

		* @param      <type>   $status  The status

		* @param      <type>   $id      The identifier

		*

		* @return     boolean  ( description_of_the_return_value )

	*/

	public function change_status_pur_order($status, $id)
	{

		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_orders', ['approve_status' => $status]);

		if ($this->db->affected_rows() > 0) {



			hooks()->apply_filters('create_goods_receipt', ['status' => $status, 'id' => $id]);

			return true;

		}

		return false;

	}



	/**

		* Gets the estimates by status.

		*

		* @param      <type>  $status  The status

		*

		* @return     <array>  The estimates by status.

	*/

	public function get_estimates_by_status($status)
	{

		$this->db->where('status', $status);

		return $this->db->get(db_prefix() . 'pur_estimates')->result_array();

	}



	/**

		* { estimate by vendor }

		*

		* @param      <type>  $vendor  The vendor

		*

		* @return     <array>  ( list estimate by vendor )

	*/

	public function estimate_by_vendor($vendor)
	{

		$this->db->where('vendor', $vendor);

		$this->db->where('status', 2);

		return $this->db->get(db_prefix() . 'pur_estimates')->result_array();

	}



	public function SumEntryItem($PO, $itemID)
	{

		$sql = 'SELECT COALESCE(sum(BilledQty),0) as BilledQty FROM ' . db_prefix() . 'history 

			where BillID = "' . $PO . '" AND ItemID = "' . $itemID . '" AND TType="P" AND TType2="Purchase" ';

		return $this->db->query($sql)->row();



	}

	public function SumEntryItemEdit($PO, $itemID, $PurchID)
	{

		$sql = 'SELECT COALESCE(sum(BilledQty),0) as BilledQty FROM ' . db_prefix() . 'history 

			where BillID = "' . $PO . '" AND ItemID = "' . $itemID . '" AND OrderID != "' . $PurchID . '" AND TType="P" AND TType2="Purchase" ';

		return $this->db->query($sql)->row();



	}



	public function add_pur_order_new($data)
	{

		if (isset($data['pur_order_detail'])) {

			$pur_order_detail = json_decode($data['pur_order_detail']);



			unset($data['pur_order_detail']);

			$es_detail = [];

			$row = [];

			$rq_val = [];

			$header = [];

			$header[] = 'ItemID';

			$header[] = 'description';

			$header[] = 'pur_unit';

			$header[] = 'CaseQty';

			$header[] = 'QTY';

			$header[] = 'Cases';

			$header[] = 'PurchRate';

			$header[] = 'disc';

			$header[] = 'DiscAmt';

			$header[] = 'batch_no';

			$header[] = 'mfg_date';

			$header[] = 'expiry_date';

			$header[] = 'GST';

			$header[] = 'CGSTAMT';

			$header[] = 'SGSTAMT';

			$header[] = 'IGSTAMT';

			$header[] = 'total_money';

			foreach ($pur_order_detail as $key => $value) {



				if ($value[0] != '') {

					$es_detail[] = array_combine($header, $value);

				}

			}

		}



		if (isset($data['charges_details'])) {

			$charges_details = json_decode($data['charges_details']);



			unset($data['charges_details']);

			$charges_detail = [];

			$header2 = [];

			$header2[] = 'AccountID';

			$header2[] = 'AccountName';

			$header2[] = 'hsn';

			$header2[] = 'qty';

			$header2[] = 'rate';

			$header2[] = 'Gst';

			$header2[] = 'CGst';

			$header2[] = 'SGst';

			$header2[] = 'IGst';

			$header2[] = 'NetAmt';

			$header2[] = 'Remark';

			$ChrTaxableAmt = 0;

			foreach ($charges_details as $key2 => $value2) {

				if ($value2[0] != '' && $value2[4] != '') {

					$ChrTaxableAmt += $value2[3] * $value2[4];

					$charges_detail[] = array_combine($header2, $value2);

				}

			}

		}



		$PlantID = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');



		if ($data['gst_num'] == '') {

			$bt = 'N';

		} else {

			$bt = 'Y';

		}



		$GodownID = $data['GodownID'];

		if ($PlantID == 1) {

			$purchase_orderNumbar = get_option('next_purchase_number_for_cspl');

		} elseif ($PlantID == 2) {

			$purchase_orderNumbar = get_option('next_purchase_number_for_cff');

		} elseif ($PlantID == 3) {

			$purchase_orderNumbar = get_option('next_purchase_number_for_cbu');

		}

		$Discamt = 0;

		$new_purchase_orderNumbar = 'PUR' . $FY . $purchase_orderNumbar;

		$ItCount = count($es_detail);

		$Transdate = to_sql_date($data['prd_date']) . " " . date('H:i:s');

		$PurchID = $data['pur_order_number'];

		$prd_date = $data['prd_date'];

		$vendor = $data['vendor'];

		$Invoiceno = $data['invoce_n'];

		$invoce_date = to_sql_date($data['invoce_date']);

		$Discamt = $data['dc_total'];

		$cgstamt = $data['CGST_amt'];

		$sgstamt = $data['SGST_AMT'];

		$RoundOffAmt = $data['Round_OFF'];

		$igstamt = $data['IGST_amt'];

		$TdsAmt = $data['TDS_amt'];

		$TdsRate = $data['TDSPer'];

		$TDSCode = $data['TDSCode'];



		$Invamt = str_replace(",", "", $data['Invoice_amt']);

		$purchase_amt = str_replace(",", "", $data['total_mn']);

		$data_array = array(

			'PlantID' => $PlantID,

			'FY' => $FY,

			'BT' => $bt,

			'PurchID' => $new_purchase_orderNumbar,

			'Transdate' => $Transdate,

			'PO_Number' => $data['po_number'],

			'AccountID' => $data['vendor'],

			'Invoiceno' => $Invoiceno,

			'Invoicedate' => to_sql_date($data['invoce_date']),

			'Purchamt' => $purchase_amt - $ChrTaxableAmt,

			'OtherCharges' => $ChrTaxableAmt,

			'Discamt' => $Discamt,

			'Invamt' => $Invamt,

			'ItCount' => $ItCount,

			'RoundOffAmt' => $RoundOffAmt,

			'cgstamt' => $cgstamt,

			'sgstamt' => $sgstamt,

			'igstamt' => $igstamt,

			'TdsAmt' => $TdsAmt,

			'TdsRate' => $TdsRate,

			'TdsSection' => $TDSCode,

			"Userid" => $_SESSION['username'],

		);



		$this->db->insert(db_prefix() . 'purchasemaster', $data_array);

		//print_r($data_array);

		if ($this->db->affected_rows() > 0) {

			$ord_n = 1;

			$this->increment_next_purchase_number();

			$i = 1;

			$ChkStatus = true;

			foreach ($es_detail as $value) {

				$item_c = $this->db->get_where(db_prefix() . 'items', array('id' => $value['ItemID'], 'PlantID' => $PlantID))->row();

				$Po_item = $this->db->get_where(db_prefix() . 'history', array('ItemID' => $item_c->ItemID, 'OrderID' => $data['po_number'], 'TType2' => 'Order'))->row();

				$PEntry_item = $this->SumEntryItem($data['po_number'], $item_c->ItemID);



				if (!empty($Po_item)) {

					$totalItem = $PEntry_item->BilledQty + $value['QTY'];

					if ($totalItem < $Po_item->BilledQty) {

						$ChkStatus = false;

					}

				}



				$get_purch_stock = $this->get_purch_stock($item_c->ItemID);

				$new_purch_stock = $get_purch_stock->PQty + $value['QTY'];



				$next_purchase_batch_number = get_option('next_purchase_batch_number');





				$this->db->where('PlantID', $PlantID);

				$this->db->LIKE('FY', $FY);

				$this->db->where('ItemID', $item_c->ItemID);

				$this->db->update(db_prefix() . 'stockmaster', [

					'PQty' => $new_purch_stock,

				]);

				$gst_devide = 0;

				$gst_igst = 0;

				if ($data['state_f'] == 'UP') {

					$gst_devide = $value['GST'] / 2;

				} else {

					$gst_igst = $value['GST'];

				}

				$Cases = $value['QTY'] / $value['pack'];



				$data_array_result = array(

					'PlantID' => $PlantID,

					'FY' => $FY,

					'cnfid' => 1,

					'OrderID' => $new_purchase_orderNumbar,

					'TransDate' => $Transdate,

					'BillID' => $data['po_number'],

					'GodownID' => $GodownID,

					'TransDate2' => $Transdate,

					'TType' => 'P',

					'TType2' => 'Hold',

					'AccountID' => $data['vendor'],

					'ItemID' => $item_c->ItemID,

					'CaseQty' => $value['CaseQty'],

					'PurchRate' => $value['PurchRate'],

					'SaleRate' => $value['PurchRate'],

					'BasicRate' => $value['PurchRate'],

					'SuppliedIn' => 1,

					'Cases' => $value['Cases'],

					'OrderQty' => $value['QTY'],

					'BilledQty' => $value['QTY'],

					'OrderAmt' => $value['PurchRate'] * $value['QTY'],

					'DiscPerc' => $value['disc'],

					'DiscAmt' => $value['DiscAmt'],

					'gst' => $value['GST'],

					'cgst' => $gst_devide,

					'sgst' => $gst_devide,

					'igst' => $gst_igst,

					'cgstamt' => $value['CGSTAMT'],

					'sgstamt' => $value['SGSTAMT'],

					'igstamt' => $value['IGSTAMT'],

					'OrderAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'],

					'ChallanAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'],

					'NetOrderAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'] + $value['CGSTAMT'] + $value['SGSTAMT'] + $value['IGSTAMT'],

					'NetChallanAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'] + $value['CGSTAMT'] + $value['SGSTAMT'] + $value['IGSTAMT'],

					'batch_no' => $value['batch_no'],

					'mfg_date' => $value['mfg_date'],

					'expiry_date' => $value['expiry_date'],

					'internal_batch_no' => $next_purchase_batch_number,

					'Ordinalno' => $i,

					'UserID' => $_SESSION['username'],

				);

				if ($this->db->insert(db_prefix() . 'history', $data_array_result)) {

					$i++;

					$this->next_purchase_batch_number();

					$ChkQcMaster = $this->ChkQCMasterByItemID($item_c->ItemID);



					if (!empty($ChkQcMaster) && $value['QTY'] > 0) {

						$QCArray = array(

							'PurchaseEntryNo' => $new_purchase_orderNumbar,

							'ItemID' => $item_c->ItemID,

							'Status' => 'N',

							'TransDate' => $Transdate,

							'UserID' => $_SESSION['username'],

						);



						$this->db->insert(db_prefix() . 'ItemWiseQCStatus', $QCArray);

					}

				}

			}



			foreach ($charges_detail as $value2) {

				$data_array_result = array(

					'PONumber' => $new_purchase_orderNumbar,

					'AccountID' => $value2['AccountID'],

					'qty' => $value2['qty'],

					'rate' => $value2['rate'],

					'gst_per' => $value2['Gst'],

					'cgst' => $value2['CGst'],

					'sgst' => $value2['SGst'],

					'igst' => $value2['IGst'],

					'amount' => $value2['NetAmt'],

					'remark' => $value2['Remark'],

					'UserID' => $_SESSION['username'],

					'TransDate' => $Transdate,

					'TransDate2' => date('Y-m-d H:i:s'),

				);

				$this->db->insert(db_prefix() . 'purchase_charges', $data_array_result);

			}

			return true;

		}

	}

	public function get_acc_bal($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$fy = $this->session->userdata('finacial_year');

		$this->db->where('PlantID', $selected_company);

		$this->db->LIKE('FY', $fy);

		$this->db->LIKE('AccountID', $id);



		return $this->db->get(db_prefix() . 'accountbalances')->row();

	}

	public function get_purch_stock($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$fy = $this->session->userdata('finacial_year');

		$this->db->where('PlantID', $selected_company);

		$this->db->LIKE('FY', $fy);

		$this->db->LIKE('ItemID', $id);



		return $this->db->get(db_prefix() . 'stockmaster')->row();

	}



	public function increment_next_purchase_number()
	{

		// Update next TAX Transaction number in settings

		$FY = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');

		if ($selected_company == 1) {

			$this->db->where('name', 'next_purchase_number_for_cspl');



		} elseif ($selected_company == 2) {

			$this->db->where('name', 'next_purchase_number_for_cff');



		} elseif ($selected_company == 3) {

			$this->db->where('name', 'next_purchase_number_for_cbu');



		}



		$this->db->set('value', 'value+1', false);

		$this->db->WHERE('FY', $FY);

		$this->db->update(db_prefix() . 'options');

	}

	public function next_purchase_batch_number()
	{

		// Update next TAX Transaction number in settings

		$FY = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');

		$this->db->where('name', 'next_purchase_batch_number');

		$this->db->set('value', 'value+1', false);

		$this->db->WHERE('FY', $FY);

		$this->db->update(db_prefix() . 'options');

	}





	public function delete_pur_order($id)
	{

		$affectedRows = 0;

		$this->db->where('pur_order', $id);

		$this->db->delete(db_prefix() . 'pur_order_detail');

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		$this->db->where('rel_id', $id);

		$this->db->where('rel_type', 'pur_order');

		$this->db->delete(db_prefix() . 'files');

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $id)) {

			delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $id);

		}



		$this->db->where('pur_order', $id);

		$this->db->delete(db_prefix() . 'pur_order_payment');

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		$this->db->where('rel_type', 'purchase_order');

		$this->db->where('rel_id', $id);

		$this->db->delete(db_prefix() . 'notes');



		$this->db->where('rel_type', 'purchase_order');

		$this->db->where('rel_id', $id);

		$this->db->delete(db_prefix() . 'reminders');



		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_orders');



		$this->db->where('rel_id', $id);

		$this->db->where('rel_type', 'pur_order');

		$this->db->delete(db_prefix() . 'taggables');

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		if ($affectedRows > 0) {

			return true;

		}

		return false;

	}



	/**

		* Gets the pur order approved.

		*

		* @return     <array>  The pur order approved.

	*/

	public function get_pur_order_approved()
	{

		$this->db->where('approve_status', 2);

		return $this->db->get(db_prefix() . 'pur_orders')->result_array();

	}



	/**

		* Adds a contract.

		*

		* @param      <type>   $data   The data

		*

		* @return     boolean  ( false) or int id contract

	*/

	public function add_contract($data)
	{



		$data['contract_value'] = reformat_currency_pur($data['contract_value']);

		$data['payment_amount'] = reformat_currency_pur($data['payment_amount']);



		$project = $this->projects_model->get($data['project']);

		$vendor_name = get_vendor_company_name($data['vendor']);

		$ven_rs = strtoupper(str_replace(' ', '', $vendor_name));

		$ct_rs = strtoupper(str_replace(' ', '', $data['contract_name']));

		if ($project) {

			$pj_rs = strtoupper(str_replace(' ', '', $project->name));

			$data['contract_number'] = $pj_rs . '-' . $ct_rs . '-' . $ven_rs;

		} else {

			$data['contract_number'] = $ct_rs . '-' . $ven_rs;

		}



		$data['add_from'] = get_staff_user_id();

		$data['start_date'] = to_sql_date($data['start_date']);

		$data['end_date'] = to_sql_date($data['end_date']);

		$data['signed_date'] = to_sql_date($data['signed_date']);

		$this->db->insert(db_prefix() . 'pur_contracts', $data);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {

			return $insert_id;

		}

		return false;



	}



	/**

		* { update contract }

		*

		* @param      <type>   $data   The data

		* @param      <type>   $id     The identifier

		*

		* @return     boolean 

	*/

	public function update_contract($data, $id)
	{

		$data['contract_value'] = reformat_currency_pur($data['contract_value']);

		$data['payment_amount'] = reformat_currency_pur($data['payment_amount']);



		$project = $this->projects_model->get($data['project']);

		$vendor_name = get_vendor_company_name($data['vendor']);

		$ven_rs = strtoupper(str_replace(' ', '', $vendor_name));

		$ct_rs = strtoupper(str_replace(' ', '', $data['contract_name']));

		if ($project) {

			$pj_rs = strtoupper(str_replace(' ', '', $project->name));

			$data['contract_number'] = $pj_rs . '-' . $ct_rs . '-' . $ven_rs;

		} else {

			$data['contract_number'] = $ct_rs . '-' . $ven_rs;

		}



		$data['add_from'] = get_staff_user_id();

		$data['start_date'] = to_sql_date($data['start_date']);

		$data['end_date'] = to_sql_date($data['end_date']);

		$data['time_payment'] = to_sql_date($data['time_payment']);

		$data['signed_date'] = to_sql_date($data['signed_date']);

		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_contracts', $data);

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* { delete contract }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean   

	*/

	public function delete_contract($id)
	{

		$this->db->where('rel_id', $id);

		$this->db->where('rel_type', 'pur_contract');

		$this->db->delete(db_prefix() . 'files');

		if ($this->db->affected_rows() > 0) {

			$affectedRows++;

		}



		if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/' . $id)) {

			delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/' . $id);

		}



		if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/contract_sign/' . $id)) {

			delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/contract_sign/' . $id);

		}



		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_contracts');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* Gets the html vendor.

		*

		* @param      <type>  $vendor  The vendor

		*

		* @return     string  The html vendor.

	*/

	public function get_html_vendor($vendor)
	{



		$vendors = $this->get_vendor($vendor);

		$html = '<table class="table border table-striped ">

			<tbody>

			<tr class="project-overview">';

		$html .= '<td width="20%" class="bold">' . _l('company') . '</td>';

		$html .= '<td>' . $vendors->company . '</td>';

		$html .= '<td width="20%" class="bold">' . _l('phonenumber') . '</td>';

		$html .= '<td>' . $vendors->phonenumber . '</td>';

		$html .= '</tr>';



		$html .= '<tr>';

		$html .= '<td width="20%" class="bold">' . _l('city') . '</td>';

		$html .= '<td>' . $vendors->city . '</td>';

		$html .= '<td width="20%" class="bold">' . _l('address') . '</td>';

		$html .= '<td>' . $vendors->address . '</td>';

		$html .= '</tr>';



		$html .= '<tr>';

		$html .= '<td width="20%" class="bold">' . _l('client_vat_number') . '</td>';

		$html .= '<td>' . $vendors->vat . '</td>';

		$html .= '<td width="20%" class="bold">' . _l('website') . '</td>';

		$html .= '<td>' . $vendors->website . '</td>';

		$html .= '</tr>';

		$html .= '</tbody>

			</table>';



		return $html;

	}



	/**

		* Gets the contract.

		*

		* @param      string  $id     The identifier

		*

		* @return     <row>,<array>  The contract.

	*/

	public function get_contract($id = '')
	{

		if ($id == '') {

			return $this->db->get(db_prefix() . 'pur_contracts')->result_array();

		} else {

			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'pur_contracts')->row();

		}

	}



	/**

		* { sign contract }

		*

		* @param      <type>   $contract  The contract

		* @param      <type>   $status    The status

		*

		* @return     boolean 

	*/

	public function sign_contract($contract, $status)
	{

		$this->db->where('id', $contract);

		$this->db->update(db_prefix() . 'pur_contracts', [

			'signed_status' => $status,

			'signed_date' => date('Y-m-d'),

			'signer' => get_staff_user_id(),

		]);

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* { check approval details }

		*

		* @param      <type>          $rel_id    The relative identifier

		* @param      <type>          $rel_type  The relative type

		*

		* @return     boolean|string 

	*/

	public function check_approval_details($rel_id, $rel_type)
	{

		$this->db->where('rel_id', $rel_id);

		$this->db->where('rel_type', $rel_type);

		$approve_status = $this->db->get(db_prefix() . 'pur_approval_details')->result_array();

		if (count($approve_status) > 0) {

			foreach ($approve_status as $value) {

				if ($value['approve'] == -1) {

					return 'reject';

				}

				if ($value['approve'] == 0) {

					$value['staffid'] = explode(', ', $value['staffid']);

					return $value;

				}

			}

			return true;

		}

		return false;

	}



	/**

		* Gets the list approval details.

		*

		* @param      <type>  $rel_id    The relative identifier

		* @param      <type>  $rel_type  The relative type

		*

		* @return     <array>  The list approval details.

	*/

	public function get_list_approval_details($rel_id, $rel_type)
	{

		$this->db->select('*');

		$this->db->where('rel_id', $rel_id);

		$this->db->where('rel_type', $rel_type);

		return $this->db->get(db_prefix() . 'pur_approval_details')->result_array();

	}



	/**

		* Sends a request approve.

		*

		* @param      <type>   $data   The data

		*

		* @return     boolean   

	*/

	public function send_request_approve($data)
	{

		if (!isset($data['status'])) {

			$data['status'] = '';

		}

		$date_send = date('Y-m-d H:i:s');

		$data_new = $this->get_approve_setting($data['rel_type'], $data['status']);

		if (!$data_new) {

			return false;

		}

		$this->delete_approval_details($data['rel_id'], $data['rel_type']);

		$list_staff = $this->staff_model->get();

		$list = [];

		$staff_addedfrom = $data['addedfrom'];

		$sender = get_staff_user_id();



		foreach ($data_new as $value) {

			$row = [];



			if ($value->approver !== 'staff') {

				$value->staff_addedfrom = $staff_addedfrom;

				$value->rel_type = $data['rel_type'];

				$value->rel_id = $data['rel_id'];



				$approve_value = $this->get_staff_id_by_approve_value($value, $value->approver);



				if (is_numeric($approve_value)) {

					$approve_value = $this->staff_model->get($approve_value)->email;

				} else {



					$this->db->where('rel_id', $data['rel_id']);

					$this->db->where('rel_type', $data['rel_type']);

					$this->db->delete('tblpur_approval_details');





					return $value->approver;

				}

				$row['approve_value'] = $approve_value;



				$staffid = $this->get_staff_id_by_approve_value($value, $value->approver);



				if (empty($staffid)) {

					$this->db->where('rel_id', $data['rel_id']);

					$this->db->where('rel_type', $data['rel_type']);

					$this->db->delete('tblpur_approval_details');





					return $value->approver;

				}



				$row['action'] = $value->action;

				$row['staffid'] = $staffid;

				$row['date_send'] = $date_send;

				$row['rel_id'] = $data['rel_id'];

				$row['rel_type'] = $data['rel_type'];

				$row['sender'] = $sender;

				$this->db->insert('tblpur_approval_details', $row);



			} else if ($value->approver == 'staff') {

				$row['action'] = $value->action;

				$row['staffid'] = $value->staff;

				$row['date_send'] = $date_send;

				$row['rel_id'] = $data['rel_id'];

				$row['rel_type'] = $data['rel_type'];

				$row['sender'] = $sender;



				$this->db->insert('tblpur_approval_details', $row);

			}

		}

		return true;

	}



	/**

		* Gets the approve setting.

		*

		* @param      <type>   $type    The type

		* @param      string   $status  The status

		*

		* @return     boolean  The approve setting.

	*/

	public function get_approve_setting($type, $status = '')
	{

		$this->db->select('*');

		$this->db->where('related', $type);

		$approval_setting = $this->db->get('tblpur_approval_setting')->row();

		if ($approval_setting) {

			return json_decode($approval_setting->setting);

		} else {

			return false;

		}

	}



	/**

		* { delete approval details }

		*

		* @param      <type>   $rel_id    The relative identifier

		* @param      <type>   $rel_type  The relative type

		*

		* @return     boolean  ( description_of_the_return_value )

	*/

	public function delete_approval_details($rel_id, $rel_type)
	{

		$this->db->where('rel_id', $rel_id);

		$this->db->where('rel_type', $rel_type);

		$this->db->delete(db_prefix() . 'pur_approval_details');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* Gets the staff identifier by approve value.

		*

		* @param      <type>  $data           The data

		* @param      string  $approve_value  The approve value

		*

		* @return     array   The staff identifier by approve value.

	*/

	public function get_staff_id_by_approve_value($data, $approve_value)
	{

		$list_staff = $this->staff_model->get();

		$list = [];

		$staffid = [];



		if ($approve_value == 'department_manager') {

			$staffid = $this->departments_model->get_staff_departments($data->staff_addedfrom)[0]['manager_id'];

		} elseif ($approve_value == 'direct_manager') {

			$staffid = $this->staff_model->get($data->staff_addedfrom)->team_manage;

		}



		return $staffid;

	}



	/**

		* Gets the staff sign.

		*

		* @param      <type>  $rel_id    The relative identifier

		* @param      <type>  $rel_type  The relative type

		*

		* @return     array   The staff sign.

	*/

	public function get_staff_sign($rel_id, $rel_type)
	{

		$this->db->select('*');



		$this->db->where('rel_id', $rel_id);

		$this->db->where('rel_type', $rel_type);

		$this->db->where('action', 'sign');

		$approve_status = $this->db->get(db_prefix() . 'pur_approval_details')->result_array();

		if (isset($approve_status)) {

			$array_return = [];

			foreach ($approve_status as $key => $value) {

				array_push($array_return, $value['staffid']);

			}

			return $array_return;

		}

		return [];

	}





	/**

		* Sends a mail.

		*

		* @param      <type>  $data   The data

	*/

	public function send_mail($data)
	{

		$this->load->model('emails_model');

		if (!isset($data['status'])) {

			$data['status'] = '';

		}

		$get_staff_enter_charge_code = '';

		$mes = 'notify_send_request_approve_project';

		$staff_addedfrom = 0;

		$additional_data = $data['rel_type'];

		$object_type = $data['rel_type'];

		switch ($data['rel_type']) {

			case 'pur_request':

				$staff_addedfrom = $this->get_purchase_request($data['rel_id'])->requester;

				$additional_data = $this->get_purchase_request($data['rel_id'])->pur_rq_name;

				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);

				$mes = 'notify_send_request_approve_pur_request';

				$mes_approve = 'notify_send_approve_pur_request';

				$mes_reject = 'notify_send_rejected_pur_request';

				$link = 'purchase/view_pur_request/' . $data['rel_id'];

				break;



			case 'pur_quotation':

				$staff_addedfrom = $this->get_estimate($data['rel_id'])->addedfrom;

				$additional_data = format_pur_estimate_number($data['rel_id']);

				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);

				$mes = 'notify_send_request_approve_pur_quotation';

				$mes_approve = 'notify_send_approve_pur_quotation';

				$mes_reject = 'notify_send_rejected_pur_quotation';

				$link = 'purchase/quotations/' . $data['rel_id'];

				break;



			case 'pur_order':

				$pur_order = $this->get_pur_order($data['rel_id']);

				$staff_addedfrom = $pur_order->addedfrom;

				$additional_data = $pur_order->pur_order_number;

				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);

				$mes = 'notify_send_request_approve_pur_order';

				$mes_approve = 'notify_send_approve_pur_order';

				$mes_reject = 'notify_send_rejected_pur_order';

				$link = 'purchase/purchase_order/' . $data['rel_id'];

				break;

			case 'payment_request':

				$pur_inv = $this->get_payment_pur_invoice($data['rel_id']);

				$staff_addedfrom = $pur_inv->requester;

				$additional_data = _l('payment_for') . ' ' . get_pur_invoice_number($pur_inv->pur_invoice);

				$list_approve_status = $this->get_list_approval_details($data['rel_id'], $data['rel_type']);

				$mes = 'notify_send_request_approve_pur_inv';

				$mes_approve = 'notify_send_approve_pur_inv';

				$mes_reject = 'notify_send_rejected_pur_inv';

				$link = 'purchase/payment_invoice/' . $data['rel_id'];

				break;

			default:



				break;

		}





		$check_approve_status = $this->check_approval_details($data['rel_id'], $data['rel_type'], $data['status']);

		if (isset($check_approve_status['staffid'])) {



			$mail_template = 'send-request-approve';



			if (!in_array(get_staff_user_id(), $check_approve_status['staffid'])) {

				foreach ($check_approve_status['staffid'] as $value) {

					$staff = $this->staff_model->get($value);

					$notified = add_notification([

						'description' => $mes,

						'touserid' => $staff->staffid,

						'link' => $link,

						'additional_data' => serialize([

							$additional_data,

						]),

					]);

					if ($notified) {

						pusher_trigger_notification([$staff->staffid]);

					}

				}

			}

		}



		if (isset($data['approve'])) {

			if ($data['approve'] == 2) {

				$mes = $mes_approve;

				$mail_template = 'send_approve';

			} else {

				$mes = $mes_reject;

				$mail_template = 'send_rejected';

			}





			$staff = $this->staff_model->get($staff_addedfrom);

			$notified = add_notification([

				'description' => $mes,

				'touserid' => $staff->staffid,

				'link' => $link,

				'additional_data' => serialize([

					$additional_data,

				]),

			]);

			if ($notified) {

				pusher_trigger_notification([$staff->staffid]);

			}



			foreach ($list_approve_status as $key => $value) {

				$value['staffid'] = explode(', ', $value['staffid']);

				if ($value['approve'] == 1 && !in_array(get_staff_user_id(), $value['staffid'])) {

					foreach ($value['staffid'] as $staffid) {



						$staff = $this->staff_model->get($staffid);

						$notified = add_notification([

							'description' => $mes,

							'touserid' => $staff->staffid,

							'link' => $link,

							'additional_data' => serialize([

								$additional_data,

							]),

						]);

						if ($notified) {

							pusher_trigger_notification([$staff->staffid]);

						}



					}

				}

			}

		}

	}



	/**

		* { update approve request }

		*

		* @param      <type>   $rel_id    The relative identifier

		* @param      <type>   $rel_type  The relative type

		* @param      <type>   $status    The status

		*

		* @return     boolean

	*/

	public function update_approve_request($rel_id, $rel_type, $status)
	{

		$data_update = [];



		switch ($rel_type) {

			case 'pur_request':

				$data_update['status'] = $status;

				$this->update_item_pur_request($rel_id);

				$this->db->where('id', $rel_id);

				$this->db->update(db_prefix() . 'pur_request', $data_update);

				return true;

				break;

			case 'pur_quotation':

				$data_update['status'] = $status;

				$this->db->where('id', $rel_id);

				$this->db->update(db_prefix() . 'pur_estimates', $data_update);

				return true;

				break;

			case 'pur_order':

				$data_update['approve_status'] = $status;

				$this->db->where('id', $rel_id);

				$this->db->update(db_prefix() . 'pur_orders', $data_update);

				return true;

				break;

			case 'payment_request':

				$data_update['approval_status'] = $status;

				$this->db->where('id', $rel_id);

				$this->db->update(db_prefix() . 'pur_invoice_payment', $data_update);



				$this->update_invoice_after_approve($rel_id);



				return true;

				break;

			default:

				return false;

				break;

		}

	}



	/**

		* { update item pur request }

		*

		* @param      $id     The identifier

	*/

	public function update_item_pur_request($id)
	{

		$pur_rq = $this->get_purchase_request($id);

		if ($pur_rq) {

			if ($pur_rq->from_items == 0) {

				$this->db->where('id', $id);

				$this->db->update(db_prefix() . 'pur_request', ['from_items' => 1]);



				$pur_rqdt = $this->get_pur_request_detail($id);

				if (count($pur_rqdt) > 0) {

					foreach ($pur_rqdt as $rqdt) {

						$item_data['description'] = $rqdt['item_text'];

						$item_data['purchase_price'] = $rqdt['unit_price'];

						$item_data['unit_id'] = $rqdt['unit_id'];

						$item_data['rate'] = '';

						$item_data['sku_code'] = '';

						$item_data['commodity_barcode'] = $this->generate_commodity_barcode();

						$item_data['commodity_code'] = $this->generate_commodity_barcode();

						$item_id = $this->add_commodity_one_item($item_data);

						$this->db->where('prd_id', $rqdt['prd_id']);

						$this->db->update(db_prefix() . 'pur_request_detail', ['ItemID' => $item_id,]);

					}

				}

			}

		}

	}



	/**

		* { update approval details }

		*

		* @param      <int>   $id     The identifier

		* @param      <type>   $data   The data

		*

		* @return     boolean 

	*/

	public function update_approval_details($id, $data)
	{

		$data['date'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_approval_details', $data);

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* { pur request pdf }

		*

		* @param      <type>  $pur_request  The pur request

		*

		* @return      ( pdf )

	*/

	public function pur_request_pdf($pur_request)
	{

		return app_pdf('pur_request', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Pur_request_pdf'), $pur_request);

	}



	/**

		* Gets the pur request pdf html.

		*

		* @param      <type>  $pur_request_id  The pur request identifier

		*

		* @return     string  The pur request pdf html.

	*/

	public function get_pur_request_pdf_html($pur_request_id)
	{

		$this->load->model('departments_model');



		$pur_request = $this->get_purchase_request($pur_request_id);

		$project = $this->projects_model->get($pur_request->project);

		$project_name = '';

		if ($project) {

			$project_name = $project->name;

		}



		$pur_request_detail = $this->get_pur_request_detail($pur_request_id);

		$company_name = get_option('invoice_company_name');

		$dpm_name = $this->departments_model->get($pur_request->department)->name;

		$address = get_option('invoice_company_address');

		$day = date('d', strtotime($pur_request->request_date));

		$month = date('m', strtotime($pur_request->request_date));

		$year = date('Y', strtotime($pur_request->request_date));

		$list_approve_status = $this->get_list_approval_details($pur_request_id, 'pur_request');



		$html = '<table class="table">

			<tbody>

			<tr>

            <td class="font_td_cpn">' . _l('purchase_company_name') . ': ' . $company_name . '</td>

            <td rowspan="3" width="" class="text-right">' . get_po_logo() . '</td>

			</tr>

			<tr>

            <td class="font_500">' . _l('address') . ': ' . $address . '</td>

			</tr>

			<tr>

            <td class="font_500">' . $pur_request->pur_rq_code . '</td>

			</tr>

			</tbody>

			</table>

			<table class="table">

			<tbody>

			<tr>

            

            <td class="td_ali_font"><h2 class="h2_style">' . mb_strtoupper(_l('purchase_request')) . '</h2></td>

			

			</tr>

			<tr>

            

            <td class="align_cen">' . _l('days') . ' ' . $day . ' ' . _l('month') . ' ' . $month . ' ' . _l('year') . ' ' . $year . '</td>

            

			</tr>

			

			</tbody>

			</table>

			<table class="table">

			<tbody>

			<tr>

            <td class="td_width_25"><h4>' . _l('requester') . ':</h4></td>

            <td class="td_width_75">' . get_staff_full_name($pur_request->requester) . '</td>

			</tr>

			<tr>

            <td class="font_500"><h4>' . _l('department') . ':</h4></td>

            <td>' . $dpm_name . '</td>

			</tr>

			<tr>

            <td class="font_500"><h4>' . _l('type') . ':</h4></td>

            <td>' . _l($pur_request->type) . '</td>

			</tr>

			<tr>

            <td class="font_500"><h4>' . _l('project') . ':</h4></td>

            <td>' . $project_name . '</td>

			</tr>

			</tbody>

			</table>

			<br><br>

			';



		$html .= '<table class="table pur_request-item">

            <thead>

			<tr class="border_tr">

			<th align="left" class="thead-dark">' . _l('items') . '</th>

			<th  class="thead-dark">' . _l('pur_unit') . '</th>

			<th align="right" class="thead-dark">' . _l('purchase_unit_price') . '</th>

			<th align="right" class="thead-dark">' . _l('purchase_quantity') . '</th>

			<th align="right" class="thead-dark">' . _l('into_money') . '</th>

			<th align="right" class="thead-dark">' . _l('inventory_quantity') . '</th>

			</tr>

            </thead>

			<tbody>';



		$tmn = 0;

		foreach ($pur_request_detail as $row) {

			$items = $this->get_items_by_id($row['ItemID']);

			$units = $this->get_units_by_id($row['unit_id']);

			$html .= '<tr class="border_tr">

				<td >' . $items->commodity_code . ' - ' . $items->description . '</td>

				<td >' . $units->unit_name . '</td>

				<td align="right">' . app_format_money($row['unit_price'], '') . '</td>

				<td align="right">' . $row['quantity'] . '</td>

				<td align="right">' . app_format_money($row['into_money'], '') . '</td>

				<td align="right">' . $row['inventory_quantity'] . '</td>

				</tr>';

			$tmn += $row['into_money'];

		}

		$html .= '</tbody>

			</table><br><br>';



		$html .= '<table class="table text-right"><tbody>';

		$html .= '<tr>

			<td width="33%"></td>

			<td>' . _l('total') . '</td>

			<td class="subtotal">

			' . app_format_money($tmn, '') . '

			</td>

			</tr>';



		$html .= ' </tbody></table>';



		$html .= '<br>

			<br>

			<br>

			<br>

			<table class="table">

			<tbody>

			<tr>';

		if (count($list_approve_status) > 0) {



			foreach ($list_approve_status as $value) {

				$html .= '<td class="td_appr">';

				if ($value['action'] == 'sign') {

					$html .= '<h3>' . mb_strtoupper(get_staff_full_name($value['staffid'])) . '</h3>';

					if ($value['approve'] == 2) {

						$html .= '<img src="' . site_url('modules/purchase/uploads/pur_request/signature/' . $pur_request->id . '/signature_' . $value['id'] . '.png') . '" class="img_style">';

					}



				} else {

					$html .= '<h3>' . mb_strtoupper(get_staff_full_name($value['staffid'])) . '</h3>';

					if ($value['approve'] == 2) {

						$html .= '<img src="' . site_url('modules/purchase/uploads/approval/approved.png') . '" class="img_style">';

					} elseif ($value['approve'] == 3) {

						$html .= '<img src="' . site_url('modules/purchase/uploads/approval/rejected.png') . '" class="img_style">';

					}



				}

				$html .= '</td>';

			}







		}

		$html .= '<td class="td_ali_font"><h3>' . mb_strtoupper(_l('purchase_requestor')) . '</h3></td>

            <td class="td_ali_font"><h3>' . mb_strtoupper(_l('purchase_treasurer')) . '</h3></td></tr>

			</tbody>

			</table>';

		$html .= '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_pdf.css') . '"  rel="stylesheet" type="text/css" />';

		return $html;

	}



	/**

		* { request quotation pdf }

		*

		* @param      <type>  $pur_request  The pur request

		*

		* @return      ( pdf )

	*/

	public function request_quotation_pdf($pur_request)
	{

		return app_pdf('pur_request', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Request_quotation_pdf'), $pur_request);

	}



	/**

		* Gets the request quotation pdf html.

		*

		* @param      <type>  $pur_request_id  The pur request identifier

		*

		* @return     string  The request quotation pdf html.

	*/

	public function get_request_quotation_pdf_html($pur_request_id)
	{

		$this->load->model('departments_model');



		$pur_request = $this->get_purchase_request($pur_request_id);

		$project = $this->projects_model->get($pur_request->project);

		$project_name = '';

		if ($project) {

			$project_name = $project->name;

		}



		$pur_request_detail = $this->get_pur_request_detail($pur_request_id);

		$company_name = get_option('invoice_company_name');

		$dpm_name = $this->departments_model->get($pur_request->department)->name;

		$address = get_option('invoice_company_address');

		$day = date('d', strtotime($pur_request->request_date));

		$month = date('m', strtotime($pur_request->request_date));

		$year = date('Y', strtotime($pur_request->request_date));

		$list_approve_status = $this->get_list_approval_details($pur_request_id, 'pur_request');



		$html = '<table class="table">

			<tbody>

			<tr>

            <td class="font_td_cpn">' . _l('purchase_company_name') . ': ' . $company_name . '</td>

            <td rowspan="3" width="" class="text-right">' . get_po_logo() . '</td>

			</tr>

			<tr>

            <td class="font_500">' . _l('address') . ': ' . $address . '</td>

			</tr>

			<tr>

            <td class="font_500">' . $pur_request->pur_rq_code . '</td>

			</tr>

			</tbody>

			</table>

			<table class="table">

			<tbody>

			<tr>

            

            <td class="td_ali_font"><h2 class="h2_style">' . mb_strtoupper(_l('purchase_request')) . '</h2></td>

			

			</tr>

			<tr>

            

            <td class="align_cen">' . _l('days') . ' ' . $day . ' ' . _l('month') . ' ' . $month . ' ' . _l('year') . ' ' . $year . '</td>

            

			</tr>

			

			</tbody>

			</table>

			<table class="table">

			<tbody>

			<tr>

            <td class="td_width_25"><h4>' . _l('requester') . ':</h4></td>

            <td class="td_width_75">' . get_staff_full_name($pur_request->requester) . '</td>

			</tr>

			<tr>

            <td class="font_500"><h4>' . _l('department') . ':</h4></td>

            <td>' . $dpm_name . '</td>

			</tr>

			<tr>

            <td class="font_500"><h4>' . _l('type') . ':</h4></td>

            <td>' . _l($pur_request->type) . '</td>

			</tr>

			<tr>

            <td class="font_500"><h4>' . _l('project') . ':</h4></td>

            <td>' . $project_name . '</td>

			</tr>

			</tbody>

			</table>

			<br><br>

			';



		$html .= '<table class="table pur_request-item">

            <thead>

			<tr class="border_tr">

			<th align="left" class="thead-dark">' . _l('items') . '</th>

			<th  class="thead-dark">' . _l('pur_unit') . '</th>

			<th align="right" class="thead-dark">' . _l('purchase_unit_price') . '</th>

			<th align="right" class="thead-dark">' . _l('purchase_quantity') . '</th>

			<th align="right" class="thead-dark">' . _l('into_money') . '</th>

			</tr>

            </thead>

			<tbody>';



		$tmn = 0;

		foreach ($pur_request_detail as $row) {

			$items = $this->get_items_by_id($row['ItemID']);

			$units = $this->get_units_by_id($row['unit_id']);

			$html .= '<tr class="border_tr">

				<td >' . $items->commodity_code . ' - ' . $items->description . '</td>

				<td >' . $units->unit_name . '</td>

				<td align="right">' . app_format_money($row['unit_price'], '') . '</td>

				<td align="right">' . $row['quantity'] . '</td>

				<td align="right">' . app_format_money($row['into_money'], '') . '</td>

				</tr>';

			$tmn += $row['into_money'];

		}

		$html .= '</tbody>

			</table><br><br>';



		$html .= '<table class="table text-right"><tbody>';

		$html .= '<tr>

			<td width="33%"></td>

			<td>' . _l('total') . '</td>

			<td class="subtotal">

			' . app_format_money($tmn, '') . '

			</td>

			</tr>';



		$html .= ' </tbody></table>';



		$html .= '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_pdf.css') . '"  rel="stylesheet" type="text/css" />';

		return $html;

	}



	/**

		* Sends a request quotation.

		*

		* @param      <type>   $data   The data

		*

		* @return     boolean

	*/

	public function send_request_quotation($data)
	{

		$staff_id = get_staff_user_id();



		$inbox = array();



		$inbox['to'] = implode(',', $data['email']);

		$inbox['sender_name'] = get_staff_full_name($staff_id);

		$inbox['subject'] = _strip_tags($data['subject']);

		$inbox['body'] = _strip_tags($data['content']);

		$inbox['body'] = nl2br_save_html($inbox['body']);

		$inbox['date_received'] = date('Y-m-d H:i:s');

		$inbox['from_email'] = get_option('smtp_email');



		if (strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0) {



			$ci = &get_instance();

			$ci->email->initialize();

			$ci->load->library('email');

			$ci->email->clear(true);

			$ci->email->from($inbox['from_email'], $inbox['sender_name']);

			$ci->email->to($inbox['to']);



			$ci->email->subject($inbox['subject']);

			$ci->email->message($inbox['body']);



			$attachment_url = site_url(PURCHASE_PATH . 'request_quotation/' . $data['pur_request_id'] . '/' . str_replace(" ", "_", $_FILES['attachment']['name']));

			$ci->email->attach($attachment_url);



			return $ci->email->send(true);

		}



		return false;

	}



	/**

		* { update purchase setting }

		*

		* @param      <type>   $data   The data

		*

		* @return     boolean 

	*/

	public function update_purchase_setting($data)
	{



		$val = $data['input_name_status'] == 'true' ? 1 : 0;

		$this->db->where('option_name', $data['input_name']);

		$this->db->update(db_prefix() . 'purchase_option', [

			'option_val' => $val,

		]);

		if ($this->db->affected_rows() > 0) {

			return true;

		} else {

			return false;

		}

	}





	/**

		* { update purchase setting }

		*

		* @param      <type>   $data   The data

		*

		* @return     boolean 

	*/

	public function update_po_number_setting($data)
	{

		$rs = 0;

		$this->db->where('option_name', 'create_invoice_by');

		$this->db->update(db_prefix() . 'purchase_option', [

			'option_val' => $data['create_invoice_by'],

		]);

		if ($this->db->affected_rows() > 0) {

			$rs++;

		}



		$this->db->where('option_name', 'pur_request_prefix');

		$this->db->update(db_prefix() . 'purchase_option', [

			'option_val' => $data['pur_request_prefix'],

		]);

		if ($this->db->affected_rows() > 0) {

			$rs++;

		}



		$this->db->where('option_name', 'pur_inv_prefix');

		$this->db->update(db_prefix() . 'purchase_option', [

			'option_val' => $data['pur_inv_prefix'],

		]);

		if ($this->db->affected_rows() > 0) {

			$rs++;

		}



		$this->db->where('option_name', 'pur_order_prefix');

		$this->db->update(db_prefix() . 'purchase_option', [

			'option_val' => $data['pur_order_prefix'],

		]);

		if ($this->db->affected_rows() > 0) {

			$rs++;

		}



		$this->db->where('option_name', 'terms_and_conditions');

		$this->db->update(db_prefix() . 'purchase_option', [

			'option_val' => $data['terms_and_conditions'],

		]);

		if ($this->db->affected_rows() > 0) {

			$rs++;

		}



		$this->db->where('option_name', 'vendor_note');

		$this->db->update(db_prefix() . 'purchase_option', [

			'option_val' => $data['vendor_note'],

		]);

		if ($this->db->affected_rows() > 0) {

			$rs++;

		}



		$this->db->where('rel_id', 0);

		$this->db->where('rel_type', 'po_logo');

		$avar = $this->db->get(db_prefix() . 'files')->row();



		if ($avar && (isset($_FILES['po_logo']['name']) && $_FILES['po_logo']['name'] != '')) {

			if (empty($avar->external)) {

				unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id . '/' . $avar->file_name);

			}

			$this->db->where('id', $avar->id);

			$this->db->delete('tblfiles');



			if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id)) {

				// Check if no avars left, so we can delete the folder also

				$other_avars = list_files(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id);

				if (count($other_avars) == 0) {

					// okey only index.html so we can delete the folder also

					delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id);

				}

			}

		}



		if (handle_po_logo()) {

			$rs++;

		}



		if ($rs > 0) {

			return true;

		}

		return false;

	}



	/**

		* Gets the purchase order attachments.

		*

		* @param      <type>  $id     The purchase order

		*

		* @return     <type>  The purchase order attachments.

	*/

	public function get_purchase_order_attachments($id)
	{



		$this->db->where('rel_id', $id);

		$this->db->where('rel_type', 'pur_order');

		return $this->db->get(db_prefix() . 'files')->result_array();

	}



	/**

		* Gets the file.

		*

		* @param      <type>   $id      The file id

		* @param      boolean  $rel_id  The relative identifier

		*

		* @return     boolean  The file.

	*/

	public function get_file($id, $rel_id = false)
	{

		$this->db->where('id', $id);

		$file = $this->db->get(db_prefix() . 'files')->row();



		if ($file && $rel_id) {

			if ($file->rel_id != $rel_id) {

				return false;

			}

		}

		return $file;

	}



	/**

		* Gets the part attachments.

		*

		* @param      <type>  $surope  The surope

		* @param      string  $id      The identifier

		*

		* @return     <type>  The part attachments.

	*/

	public function get_purorder_attachments($surope, $id = '')
	{

		// If is passed id get return only 1 attachment

		if (is_numeric($id)) {

			$this->db->where('id', $id);

		} else {

			$this->db->where('rel_id', $assets);

		}

		$this->db->where('rel_type', 'pur_order');

		$result = $this->db->get(db_prefix() . 'files');

		if (is_numeric($id)) {

			return $result->row();

		}



		return $result->result_array();

	}



	/**

		* { delete purorder attachment }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean 

	*/

	public function delete_purorder_attachment($id)
	{

		$attachment = $this->get_purorder_attachments('', $id);

		$deleted = false;

		if ($attachment) {

			if (empty($attachment->external)) {

				unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $attachment->rel_id . '/' . $attachment->file_name);

			}

			$this->db->where('id', $attachment->id);

			$this->db->delete('tblfiles');

			if ($this->db->affected_rows() > 0) {

				$deleted = true;

			}



			if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $attachment->rel_id)) {

				// Check if no attachments left, so we can delete the folder also

				$other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $attachment->rel_id);

				if (count($other_attachments) == 0) {

					// okey only index.html so we can delete the folder also

					delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_order/' . $attachment->rel_id);

				}

			}

		}



		return $deleted;

	}



	/**

		* Gets the payment purchase order.

		*

		* @param      <type>  $id     The purcahse order id

		*

		* @return     <type>  The payment purchase order.

	*/

	public function get_payment_purchase_order($id)
	{

		$this->db->where('pur_order', $id);

		return $this->db->get(db_prefix() . 'pur_order_payment')->result_array();

	}



	/**

		* Adds a payment.

		*

		* @param      <type>   $data       The data

		* @param      <type>   $pur_order  The pur order id

		*

		* @return     boolean  ( return id payment after insert )

	*/

	public function add_payment($data, $pur_order)
	{

		$data['date'] = to_sql_date($data['date']);

		$data['daterecorded'] = date('Y-m-d H:i:s');

		$data['amount'] = str_replace(',', '', $data['amount']);

		$data['pur_order'] = $pur_order;



		$this->db->insert(db_prefix() . 'pur_order_payment', $data);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {

			return $insert_id;

		}

		return false;

	}



	/**

		* { delete payment }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean  ( delete payment )

	*/

	public function delete_payment($id)
	{

		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_order_payment');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* { purorder pdf }

		*

		* @param      <type>  $pur_request  The pur request

		*

		* @return     <type>  ( purorder pdf )

	*/

	public function purorder_pdf($pur_order)
	{

		return app_pdf('pur_order', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Pur_order_pdf'), $pur_order);

	}





	/**

		* Gets the pur request pdf html.

		*

		* @param      <type>  $pur_request_id  The pur request identifier

		*

		* @return     string  The pur request pdf html.

	*/

	public function get_purorder_pdf_html($pur_order_id)
	{





		$pur_order = $this->get_pur_order($pur_order_id);

		$pur_order_detail = $this->get_pur_order_detail($pur_order_id);

		$company_name = get_option('invoice_company_name');

		$vendor = $this->get_vendor($pur_order->vendor);





		$address = '';

		$vendor_name = '';

		$ship_to = '';

		if ($vendor) {

			$address = $vendor->address;

			$vendor_name = $vendor->company;

			$ship_to = $vendor->shipping_street . '  ' . $vendor->shipping_city . '  ' . $vendor->shipping_state;

			if ($vendor->shipping_street == '' && $vendor->shipping_city == '' && $vendor->shipping_state == '') {

				$ship_to = $address;

			}

		}



		$day = _d($pur_order->order_date);





		$html = '<table class="table">

			<tbody>

			<tr>

            <td rowspan="6" class="text-left" width="70%">

            ' . get_po_logo(150) . '

			<br>' . format_organization_info() . '

            </td>

            <td class="text-right" width="30%">

			<strong class="fsize20">' . mb_strtoupper(_l('purchase_order')) . '</strong><br>

			<strong>' . mb_strtoupper($pur_order->pur_order_number) . '</strong><br>

            </td>

			</tr>

			

			<tr>

            <td class="text-right" width="30%">

			<br><strong>' . _l('vendor') . '</strong>    

			<br>' . $vendor_name . '

			<br>' . $address . '

            </td>

            <td></td>

			</tr>

			

			<tr>

            <td></td>

			</tr>

			<tr>

            <td class="text-right" width="30%">

			<br><strong>' . _l('pur_ship_to') . '</strong>    

			<br>' . $ship_to . '

            </td>

            <td></td>

			</tr>

			

			<tr>

            <td></td>

			</tr>

			<tr>

            <td class="text-right">' . _l('order_date') . ': ' . $day . '</td>

            <td></td>

			</tr>

			

			</tbody>

			</table>

			<br><br><br>

			';



		$html .= '<table class="table purorder-item">

			<thead>

			<tr>

            <th class="thead-dark">' . _l('items') . '</th>

            <th class="thead-dark" align="right">' . _l('purchase_unit_price') . '</th>

            <th class="thead-dark" align="right">' . _l('purchase_quantity') . '</th>

			

            <th class="thead-dark" align="right">' . _l('tax') . '</th>

			

            <th class="thead-dark" align="right">' . _l('discount') . '</th>

            <th class="thead-dark" align="right">' . _l('total') . '</th>

			</tr>

			</thead>

			<tbody>';

		$t_mn = 0;

		foreach ($pur_order_detail as $row) {

			$items = $this->get_items_by_id($row['ItemID']);

			$units = $this->get_units_by_id($row['unit_id']);

			$html .= '<tr nobr="true" class="sortable">

				<td >' . $items->commodity_code . ' - ' . $items->description . '</td>

				<td align="right">' . app_format_money($row['unit_price'], '') . '</td>

				<td align="right">' . $row['quantity'] . '</td>

				

				<td align="right">' . app_format_money($row['total'] - $row['into_money'], '') . '</td>

				

				<td align="right">' . app_format_money($row['discount_money'], '') . '</td>

				<td align="right">' . app_format_money($row['total_money'], '') . '</td>

				</tr>';



			$t_mn += $row['total_money'];

		}

		$html .= '</tbody>

			</table><br><br>';



		$html .= '<table class="table text-right"><tbody>';

		if ($pur_order->discount_total > 0) {

			$html .= '<tr id="subtotal">

				<td width="33%"></td>

				<td>' . _l('subtotal') . ' </td>

				<td class="subtotal">

				' . app_format_money($t_mn, '') . '

				</td>

				</tr>

				<tr id="subtotal">

				<td width="33%"></td>

				<td>' . _l('discount(%)') . '(%)' . '</td>

				<td class="subtotal">

				' . app_format_money($pur_order->discount_percent, '') . ' %' . '

				</td>

				</tr>

				<tr id="subtotal">

				<td width="33%"></td>

				<td>' . _l('discount(money)') . '</td>

				<td class="subtotal">

				' . app_format_money($pur_order->discount_total, '') . '

				</td>

				</tr>';

		}

		$html .= '<tr id="subtotal">

			<td width="33%"></td>

			<td>' . _l('total') . '</td>

			<td class="subtotal">

			' . app_format_money($pur_order->total, '') . '

			</td>

			</tr>';



		$html .= ' </tbody></table>';



		$html .= '<div class="col-md-12 mtop15">

			<h4>' . _l('terms_and_conditions') . ':</h4><p>' . $pur_order->terms . '</p>

			

			</div>';

		$html .= '<br>';

		$html .= '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_pdf.css') . '?v=' . PURCHASE_REVISION . '"  rel="stylesheet" type="text/css" />';

		return $html;

	}



	/**

		* clear signature

		*

		* @param      string   $id     The identifier

		*

		* @return     boolean  ( description_of_the_return_value )

	*/

	public function clear_signature($id)
	{

		$this->db->select('signature');

		$this->db->where('id', $id);

		$contract = $this->db->get(db_prefix() . 'pur_contracts')->row();



		if ($contract) {

			$this->db->where('id', $id);

			$this->db->update(db_prefix() . 'pur_contracts', ['signed_status' => 'not_signed']);



			if (!empty($contract->signature)) {

				unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/contract_sign/' . $id . '/' . $contract->signature);

			}



			return true;

		}





		return false;

	}



	/**

		* get data Purchase statistics by cost

		*

		* @param      string  $year   The year

		*

		* @return     array

	*/

	public function cost_of_purchase_orders_analysis($year = '')
	{

		if ($year == '') {

			$year = date('Y');

		}

		$query = $this->db->query('SELECT DATE_FORMAT(order_date, "%m") AS month, Sum((SELECT SUM(total_money) as total FROM ' . db_prefix() . 'pur_order_detail where pur_order = ' . db_prefix() . 'pur_orders.id)) as total 

            FROM ' . db_prefix() . 'pur_orders where DATE_FORMAT(order_date, "%Y") = ' . $year . '

            group by month')->result_array();

		$result = [];

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$cost = [];

		$rs = 0;

		foreach ($query as $value) {

			if ($value['total'] > 0) {

				$result[$value['month'] - 1] = (double) $value['total'];

			}

		}

		return $result;

	}



	/**

		* get data Purchase statistics by number of purchase orders

		*

		* @param      string  $year   The year

		*

		* @return     array

	*/

	public function number_of_purchase_orders_analysis($year = '')
	{

		if ($year == '') {

			$year = date('Y');

		}

		$query = $this->db->query('SELECT DATE_FORMAT(order_date, "%m") AS month, Count(*) as count 

            FROM ' . db_prefix() . 'pur_orders where DATE_FORMAT(order_date, "%Y") = ' . $year . '

            group by month')->result_array();

		$result = [];

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$result[] = 0;

		$cost = [];

		$rs = 0;

		foreach ($query as $value) {

			if ($value['count'] > 0) {

				$result[$value['month'] - 1] = (int) $value['count'];

			}

		}

		return $result;

	}



	/**

		* Gets the payment by vendor.

		*

		* @param      <type>  $vendor  The vendor

	*/

	public function get_payment_by_vendor($vendor)
	{

		return $this->db->query('select pop.pur_order, pop.id as pop_id, pop.amount, pop.date, pop.paymentmode, pop.transactionid, po.pur_order_name from ' . db_prefix() . 'pur_order_payment pop left join ' . db_prefix() . 'pur_orders po on po.id = pop.pur_order where po.vendor = ' . $vendor)->result_array();

	}



	/**

		* get unit add item 

		* @return array

	*/

	public function get_unit_add_item()
	{

		return $this->db->query('select * from tblware_unit_type where display = 1 order by tblware_unit_type.order asc ')->result_array();

	}



	/**

		* get commodity

		* @param  boolean $id

		* @return array or object

	*/

	public function get_item($id = false)
	{



		if (is_numeric($id)) {

			$this->db->where('id', $id);



			return $this->db->get(db_prefix() . 'items')->row();

		}

		if ($id == false) {

			return $this->db->query('select * from tblitems')->result_array();

		}



	}

	public function get_item_data($id = false)
	{





		// return $this->db->query('select * from tblitems')->result_array();

		$data = $this->db->query('SELECT `ItemGroupID` FROM `itemgroups` WHERE `MainItemGroupID` = 2 ORDER BY `itemgroups`.`ItemGroupID` asc ')->result_array();

		$DivisionID = array();

		foreach ($data as $value) {



			array_push($DivisionID, $value['ItemGroupID']);

		}



		$this->db->select('id,description');

		$this->db->where_in('DivisionID', $DivisionID);

		return $contract = $this->db->get('tblitems')->result_array();

		//   return $this->db->last_query();





	}



	/**

		* get inventory commodity

		* @param  integer $commodity_id 

		* @return array            

	*/

	public function get_inventory_item($commodity_id)
	{

		$sql = 'SELECT ' . db_prefix() . 'warehouse.warehouse_code, sum(inventory_number) as inventory_number, unit_name FROM ' . db_prefix() . 'inventory_manage 

            LEFT JOIN ' . db_prefix() . 'items on ' . db_prefix() . 'inventory_manage.commodity_id = ' . db_prefix() . 'items.id 

            LEFT JOIN ' . db_prefix() . 'ware_unit_type on ' . db_prefix() . 'items.unit_id = ' . db_prefix() . 'ware_unit_type.unit_type_id

            LEFT JOIN ' . db_prefix() . 'warehouse on ' . db_prefix() . 'inventory_manage.warehouse_id = ' . db_prefix() . 'warehouse.warehouse_id

			where commodity_id = ' . $commodity_id . ' group by ' . db_prefix() . 'inventory_manage.warehouse_id';

		return $this->db->query($sql)->result_array();





	}



	/**

		* get warehourse attachments

		* @param  integer $commodity_id 

		* @return array               

	*/

	public function get_item_attachments($commodity_id)
	{



		$this->db->order_by('dateadded', 'desc');

		$this->db->where('rel_id', $commodity_id);

		$this->db->where('rel_type', 'commodity_item_file');



		return $this->db->get(db_prefix() . 'files')->result_array();



	}



	/**

		* generate commodity barcode

		*

		* @return     string 

	*/

	public function generate_commodity_barcode()
	{

		$item = false;

		do {

			$length = 11;

			$chars = '0123456789';

			$count = mb_strlen($chars);

			$password = '';

			for ($i = 0; $i < $length; $i++) {

				$index = rand(0, $count - 1);

				$password .= mb_substr($chars, $index, 1);

			}

			$this->db->where('commodity_barcode', $password);

			$item = $this->db->get(db_prefix() . 'items')->row();

		} while ($item);



		return $password;

	}



	/**

		* add commodity one item

		* @param array $data

		* @return integer 

	*/

	public function add_commodity_one_item($data)
	{

		/*add data tblitem*/

		$data['rate'] = reformat_currency_pur($data['rate']);

		$data['purchase_price'] = reformat_currency_pur($data['purchase_price']);



		/*create sku code*/

		if ($data['sku_code'] != '') {

			$data['sku_code'] = $data['sku_code'];

		} else {

			$data['sku_code'] = $this->create_sku_code('', '');

		}



		/*create sku code*/



		$this->db->insert(db_prefix() . 'items', $data);

		$insert_id = $this->db->insert_id();



		/*add data tblinventory*/

		return $insert_id;



	}





	/**

		* update commodity one item

		* @param  array $data 

		* @param  integer $id   

		* @return boolean        

	*/

	public function update_commodity_one_item($data, $id)
	{

		/*add data tblitem*/

		$data['rate'] = reformat_currency_pur($data['rate']);

		$data['purchase_price'] = reformat_currency_pur($data['purchase_price']);





		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'items', $data);





		return true;

	}



	/**

		* create sku code 

		* @param  int commodity_group 

		* @param  int sub_group 

		* @return string

	*/

	public function create_sku_code($commodity_group, $sub_group)
	{

		// input  commodity group, sub group

		//get commodity group from id

		$group_character = '';

		if (isset($commodity_group)) {



			$sql_group_where = 'SELECT * FROM ' . db_prefix() . 'items_groups where id = "' . $commodity_group . '"';

			$group_value = $this->db->query($sql_group_where)->row();

			if ($group_value) {



				if ($group_value->commodity_group_code != '') {

					$group_character = mb_substr($group_value->commodity_group_code, 0, 1, "UTF-8") . '-';



				}

			}



		}



		//get sku code from sku id

		$sub_code = '';









		$sql_where = 'SELECT * FROM ' . db_prefix() . 'items order by id desc limit 1';

		$last_commodity_id = $this->db->query($sql_where)->row();

		if ($last_commodity_id) {

			$next_commodity_id = (int) $last_commodity_id->id + 1;

		} else {

			$next_commodity_id = 1;

		}

		$commodity_id_length = strlen((string) $next_commodity_id);



		$commodity_str_betwen = '';



		$create_candidate_code = '';



		switch ($commodity_id_length) {

			case 1:

				$commodity_str_betwen = '000';

				break;

			case 2:

				$commodity_str_betwen = '00';

				break;

			case 3:

				$commodity_str_betwen = '0';

				break;



			default:

				$commodity_str_betwen = '0';

				break;

		}





		return $group_character . $sub_code . $commodity_str_betwen . $next_commodity_id; // X_X_000.id auto increment





	}





	/**

		* get commodity group add commodity

		* @return array

	*/

	public function get_commodity_group_add_commodity()
	{



		return $this->db->query('select * from tblItemsDivisionMaster where display = 1 order by tblItemsDivisionMaster.order asc ')->result_array();

	}

	public function get_commodity_group_add_commodity_data()
	{



		return $this->db->query('SELECT * FROM `itemgroups` WHERE `MainItemGroupID` = 2 ORDER BY `itemgroups`.`ItemGroupID` asc ')->result_array();

	}



	//delete _commodity_file file for any 

	/**

		* delete commodity file

		* @param  integer $attachment_id 

		* @return boolean                

	*/

	public function delete_commodity_file($attachment_id)
	{

		$deleted = false;

		$attachment = $this->get_commodity_attachments_delete($attachment_id);



		if ($attachment) {

			if (empty($attachment->external)) {

				if (file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $attachment->rel_id . '/' . $attachment->file_name)) {

					unlink(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $attachment->rel_id . '/' . $attachment->file_name);

				} else {

					unlink('modules/warehouse/uploads/item_img/' . $attachment->rel_id . '/' . $attachment->file_name);

				}

			}

			$this->db->where('id', $attachment->id);

			$this->db->delete(db_prefix() . 'files');

			if ($this->db->affected_rows() > 0) {

				$deleted = true;

				log_activity('commodity Attachment Deleted [commodityID: ' . $attachment->rel_id . ']');

			}

			if (file_exists(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $attachment->rel_id . '/' . $attachment->file_name)) {

				if (is_dir(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $attachment->rel_id)) {

					// Check if no attachments left, so we can delete the folder also

					$other_attachments = list_files(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $attachment->rel_id);

					if (count($other_attachments) == 0) {

						// okey only index.html so we can delete the folder also

						delete_dir(PURCHASE_MODULE_ITEM_UPLOAD_FOLDER . $attachment->rel_id);

					}

				}

			} else {

				if (is_dir(site_url('modules/warehouse/uploads/item_img/') . $attachment->rel_id)) {

					// Check if no attachments left, so we can delete the folder also

					$other_attachments = list_files(site_url('modules/warehouse/uploads/item_img/') . $attachment->rel_id);

					if (count($other_attachments) == 0) {

						// okey only index.html so we can delete the folder also

						delete_dir(site_url('modules/warehouse/uploads/item_img/') . $attachment->rel_id);

					}

				}

			}

		}



		return $deleted;

	}



	/**

		* get commodity attachments delete

		* @param  integer $id 

		* @return object     

	*/

	public function get_commodity_attachments_delete($id)
	{



		if (is_numeric($id)) {

			$this->db->where('id', $id);



			return $this->db->get(db_prefix() . 'files')->row();

		}

	}



	/**

		* get unit type

		* @param  boolean $id

		* @return array or object

	*/

	public function get_unit_type($id = false)
	{



		if (is_numeric($id)) {

			$this->db->where('unit_type_id', $id);



			return $this->db->get(db_prefix() . 'ware_unit_type')->row();

		}

		if ($id == false) {

			return $this->db->query('select * from tblware_unit_type')->result_array();

		}



	}



	/**

		* add unit type 

		* @param array  $data

		* @param boolean $id

		* return boolean

	*/

	public function add_unit_type($data, $id = false)
	{



		$unit_type = str_replace(', ', '|/\|', $data['hot_unit_type']);

		$data_unit_type = explode(',', $unit_type);

		$results = 0;

		$results_update = '';

		$flag_empty = 0;





		foreach ($data_unit_type as $unit_type_key => $unit_type_value) {

			if ($unit_type_value == '') {

				$unit_type_value = 0;

			}

			if (($unit_type_key + 1) % 6 == 0) {

				$arr_temp['note'] = str_replace('|/\|', ', ', $unit_type_value);



				if ($id == false && $flag_empty == 1) {

					$this->db->insert(db_prefix() . 'ware_unit_type', $arr_temp);

					$insert_id = $this->db->insert_id();

					if ($insert_id) {

						$results++;

					}

				}

				if (is_numeric($id) && $flag_empty == 1) {

					$this->db->where('unit_type_id', $id);

					$this->db->update(db_prefix() . 'ware_unit_type', $arr_temp);

					if ($this->db->affected_rows() > 0) {

						$results_update = true;

					} else {

						$results_update = false;

					}

				}

				$flag_empty = 0;

				$arr_temp = [];

			} else {



				switch (($unit_type_key + 1) % 6) {

					case 1:

						$arr_temp['unit_code'] = str_replace('|/\|', ', ', $unit_type_value);



						if ($unit_type_value != '0') {

							$flag_empty = 1;

						}

						break;

					case 2:

						$arr_temp['unit_name'] = str_replace('|/\|', ', ', $unit_type_value);

						break;

					case 3:

						$arr_temp['unit_symbol'] = $unit_type_value;

						break;

					case 4:

						$arr_temp['order'] = $unit_type_value;

						break;

					case 5:

						if ($unit_type_value == 'yes') {

							$display_value = 1;

						} else {

							$display_value = 0;

						}

						$arr_temp['display'] = $display_value;

						break;

				}

			}



		}



		if ($id == false) {

			return $results > 0 ? true : false;

		} else {

			return $results_update;

		}



	}



	/**

		* delete unit type

		* @param  integer $id

		* @return boolean

	*/

	public function delete_unit_type($id)
	{

		$this->db->where('unit_type_id', $id);

		$this->db->delete(db_prefix() . 'ware_unit_type');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* delete commodity

		* @param  integer $id

		* @return boolean

	*/

	public function delete_commodity($id)
	{

		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'items');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* { mark converted pur order }

		*

		* @param      <int>  $pur_order  The pur order

		* @param      <int>  $expense    The expense

	*/

	public function mark_converted_pur_order($pur_order, $expense)
	{

		$this->db->where('id', $pur_order);

		$this->db->update(db_prefix() . 'pur_orders', ['expense_convert' => $expense]);

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* { delete purchase vendor attachment }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean  

	*/

	public function delete_ic_attachment($id)
	{

		$attachment = $this->get_ic_attachments('', $id);

		$deleted = false;

		if ($attachment) {

			if (empty($attachment->external)) {

				unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_vendor/' . $attachment->rel_id . '/' . $attachment->file_name);

			}

			$this->db->where('id', $attachment->id);

			$this->db->delete('tblfiles');

			if ($this->db->affected_rows() > 0) {

				$deleted = true;

			}



			if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_vendor/' . $attachment->rel_id)) {

				// Check if no attachments left, so we can delete the folder also

				$other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_vendor/' . $attachment->rel_id);

				if (count($other_attachments) == 0) {

					// okey only index.html so we can delete the folder also

					delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_vendor/' . $attachment->rel_id);

				}

			}

		}



		return $deleted;

	}



	/**

		* Gets the ic attachments.

		*

		* @param      <type>  $assets  The assets

		* @param      string  $id      The identifier

		*

		* @return     <type>  The ic attachments.

	*/

	public function get_ic_attachments($assets, $id = '')
	{

		// If is passed id get return only 1 attachment

		if (is_numeric($id)) {

			$this->db->where('id', $id);

		} else {

			$this->db->where('rel_id', $assets);

		}

		$this->db->where('rel_type', 'pur_vendor');

		$result = $this->db->get('tblfiles');

		if (is_numeric($id)) {

			return $result->row();

		}



		return $result->result_array();

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

		$client = $this->db->get(db_prefix() . 'pur_contacts')->row();



		if (!app_hasher()->CheckPassword($oldPassword, $client->password)) {

			return [

				'old_password_not_match' => true,

			];

		}



		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_contacts', [

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

		* Gets the pur order by vendor.

		*

		* @param      <type>  $vendor  The vendor

	*/

	public function get_pur_order_by_vendor($vendor)
	{

		$this->db->where('vendor', $vendor);

		return $this->db->get(db_prefix() . 'pur_orders')->result_array();

	}



	public function get_contracts_by_vendor($vendor)
	{

		$this->db->where('vendor', $vendor);

		return $this->db->get(db_prefix() . 'pur_contracts')->result_array();

	}



	/**

		* @param  integer ID

		* @param  integer Status ID

		* @return boolean

		* Update contact status Active/Inactive

	*/

	public function change_contact_status($id, $status)
	{



		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_contacts', [

			'active' => $status,

		]);

		if ($this->db->affected_rows() > 0) {



			return true;

		}



		return false;

	}



	/**

		* Gets the item by group.

		*

		* @param        $group  The group

		*

		* @return      The item by group.

	*/

	public function get_item_by_group($group)
	{

		$this->db->where('DivisionID', $group);

		return $this->db->get(db_prefix() . 'items')->result_array();

	}



	/**

		* Adds vendor items.

		*

		* @param      $data   The data

		*

		* @return     boolean 

	*/

	public function add_vendor_items($data)
	{

		$rs = 0;

		$data['add_from'] = get_staff_user_id();

		$data['datecreate'] = date('Y-m-d');

		foreach ($data['items'] as $val) {

			$this->db->insert(db_prefix() . 'pur_vendor_items', [

				'vendor' => $data['vendor'],

				'group_items' => $data['group_item'],

				'items' => $val,

				'add_from' => $data['add_from'],

				'datecreate' => $data['datecreate'],

			]);

			$insert_id = $this->db->insert_id();



			if ($insert_id) {

				$rs++;

			}

		}



		if ($rs > 0) {

			return true;

		}

		return false;

	}



	/**

		* { delete vendor items }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean  

	*/

	public function delete_vendor_items($id)
	{

		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_vendor_items');

		if ($this->db->affected_rows() > 0) {



			return true;

		}

		return false;

	}



	/**

		* Gets the item by vendor.

		*

		* @param      $vendor  The vendor

	*/

	public function get_item_by_vendor($vendor)
	{



		$this->db->where('vendor', $vendor);

		return $this->db->get(db_prefix() . 'pur_vendor_items')->result_array();

	}



	/**

		* Gets the items.

		*

		* @return     <array>  The items.

	*/

	public function get_items_hs_vendor($vendor)
	{

		return $this->db->query('select items as id, CONCAT(it.commodity_code," - " ,it.description) as label from ' . db_prefix() . 'pur_vendor_items pit LEFT JOIN ' . db_prefix() . 'items it ON it.id = pit.items where pit.vendor = ' . $vendor)->result_array();

	}



	/**

		* get commodity group type

		* @param  boolean $id

		* @return array or object

	*/

	public function get_commodity_group_type($id = false)
	{



		if (is_numeric($id)) {

			$this->db->where('id', $id);



			return $this->db->get(db_prefix() . 'items_groups')->row();

		}

		if ($id == false) {

			return $this->db->query('select * from tblItemsDivisionMaster')->result_array();

		}



	}



	/**

		* add commodity group type

		* @param array  $data

		* @param boolean $id

		* return boolean

	*/

	public function add_commodity_group_type($data, $id = false)
	{

		$data['commodity_group'] = str_replace(', ', '|/\|', $data['hot_commodity_group_type']);



		$data_commodity_group_type = explode(',', $data['commodity_group']);

		$results = 0;

		$results_update = '';

		$flag_empty = 0;



		foreach ($data_commodity_group_type as $commodity_group_type_key => $commodity_group_type_value) {

			if ($commodity_group_type_value == '') {

				$commodity_group_type_value = 0;

			}

			if (($commodity_group_type_key + 1) % 5 == 0) {



				$arr_temp['note'] = str_replace('|/\|', ', ', $commodity_group_type_value);



				if ($id == false && $flag_empty == 1) {

					$this->db->insert(db_prefix() . 'items_groups', $arr_temp);

					$insert_id = $this->db->insert_id();

					if ($insert_id) {

						$results++;

					}

				}

				if (is_numeric($id) && $flag_empty == 1) {

					$this->db->where('id', $id);

					$this->db->update(db_prefix() . 'items_groups', $arr_temp);

					if ($this->db->affected_rows() > 0) {

						$results_update = true;

					} else {

						$results_update = false;

					}

				}



				$flag_empty = 0;

				$arr_temp = [];

			} else {



				switch (($commodity_group_type_key + 1) % 5) {

					case 1:

						if (is_numeric($id)) {

							//update

							$arr_temp['commodity_group_code'] = str_replace('|/\|', ', ', $commodity_group_type_value);

							$flag_empty = 1;



						} else {

							//add

							$arr_temp['commodity_group_code'] = str_replace('|/\|', ', ', $commodity_group_type_value);



							if ($commodity_group_type_value != '0') {

								$flag_empty = 1;

							}



						}

						break;

					case 2:

						$arr_temp['name'] = str_replace('|/\|', ', ', $commodity_group_type_value);

						break;

					case 3:

						$arr_temp['order'] = $commodity_group_type_value;

						break;

					case 4:

						//display 1: display (yes) , 0: not displayed (no)

						if ($commodity_group_type_value == 'yes') {

							$display_value = 1;

						} else {

							$display_value = 0;

						}

						$arr_temp['display'] = $display_value;

						break;

				}

			}



		}



		if ($id == false) {

			return $results > 0 ? true : false;

		} else {

			return $results_update;

		}



	}



	/**

		* delete commodity group type

		* @param  integer $id

		* @return boolean

	*/

	public function delete_commodity_group_type($id)
	{

		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'items_groups');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* get sub group

		* @param  boolean $id

		* @return array  or object

	*/

	public function get_sub_group($id = false)
	{



		if (is_numeric($id)) {

			$this->db->where('id', $id);



			return $this->db->get(db_prefix() . 'wh_sub_group')->row();

		}

		if ($id == false) {

			return $this->db->query('select * from tblwh_sub_group')->result_array();

		}



	}



	/**

		* get item group

		* @return array 

	*/

	public function get_item_group()
	{

		return $this->db->query('select id as id, CONCAT(name,"_",commodity_group_code) as label from ' . db_prefix() . 'items_groups')->result_array();

	}



	/**

		* add sub group

		* @param array  $data

		* @param boolean $id

		* @return boolean

	*/

	public function add_sub_group($data, $id = false)
	{

		$commodity_type = str_replace(', ', '|/\|', $data['hot_sub_group']);



		$data_commodity_type = explode(',', $commodity_type);

		$results = 0;

		$results_update = '';

		$flag_empty = 0;



		foreach ($data_commodity_type as $commodity_type_key => $commodity_type_value) {

			if ($commodity_type_value == '') {

				$commodity_type_value = 0;

			}

			if (($commodity_type_key + 1) % 6 == 0) {

				$arr_temp['note'] = str_replace('|/\|', ', ', $commodity_type_value);



				if ($id == false && $flag_empty == 1) {

					$this->db->insert(db_prefix() . 'wh_sub_group', $arr_temp);

					$insert_id = $this->db->insert_id();

					if ($insert_id) {

						$results++;

					}

				}

				if (is_numeric($id) && $flag_empty == 1) {

					$this->db->where('id', $id);

					$this->db->update(db_prefix() . 'wh_sub_group', $arr_temp);

					if ($this->db->affected_rows() > 0) {

						$results_update = true;

					} else {

						$results_update = false;

					}

				}

				$flag_empty = 0;

				$arr_temp = [];

			} else {



				switch (($commodity_type_key + 1) % 6) {

					case 1:

						$arr_temp['sub_group_code'] = str_replace('|/\|', ', ', $commodity_type_value);

						if ($commodity_type_value != '0') {

							$flag_empty = 1;

						}

						break;

					case 2:

						$arr_temp['sub_group_name'] = str_replace('|/\|', ', ', $commodity_type_value);

						break;

					case 3:

						$arr_temp['DivisionID'] = $commodity_type_value;

						break;

					case 4:

						$arr_temp['order'] = $commodity_type_value;

						break;

					case 5:

						//display 1: display (yes) , 0: not displayed (no)

						if ($commodity_type_value == 'yes') {

							$display_value = 1;

						} else {

							$display_value = 0;

						}

						$arr_temp['display'] = $display_value;

						break;

				}

			}



		}



		if ($id == false) {

			return $results > 0 ? true : false;

		} else {

			return $results_update;

		}



	}



	/**

		* delete_sub_group

		* @param  integer $id

		* @return boolean

	*/

	public function delete_sub_group($id)
	{

		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'wh_sub_group');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* list subgroup by group

		* @param  integer $group 

		* @return string        

	*/

	public function list_subgroup_by_group($group)
	{

		$this->db->where('DivisionID', $group);

		$arr_subgroup = $this->db->get(db_prefix() . 'wh_sub_group')->result_array();



		$options = '';

		if (count($arr_subgroup) > 0) {

			foreach ($arr_subgroup as $value) {



				$options .= '<option value="' . $value['id'] . '">' . $value['sub_group_name'] . '</option>';

			}



		}

		return $options;



	}



	/**

		* get item tag filter

		* @return array 

	*/

	public function get_item_tag_filter()
	{

		return $this->db->query('select * FROM ' . db_prefix() . 'taggables left join ' . db_prefix() . 'tags on ' . db_prefix() . 'taggables.tag_id =' . db_prefix() . 'tags.id where ' . db_prefix() . 'taggables.rel_type = "pur_order"')->result_array();

	}



	/**

		* Gets the pur contract attachment.

		*

		* @param        $id     The identifier

	*/

	public function get_pur_contract_attachment($id)
	{

		$this->db->where('rel_id', $id);

		$this->db->where('rel_type', 'pur_contract');

		return $this->db->get(db_prefix() . 'files')->result_array();

	}



	/**

		* Gets the pur contract attachments.

		*

		* @param        $assets  The assets

		* @param      string  $id      The identifier

		*

		* @return       The pur contract attachments.

	*/

	public function get_pur_contract_attachments($assets, $id = '')
	{

		// If is passed id get return only 1 attachment

		if (is_numeric($id)) {

			$this->db->where('id', $id);

		} else {

			$this->db->where('rel_id', $assets);

		}

		$this->db->where('rel_type', 'pur_contract');

		$result = $this->db->get(db_prefix() . 'files');

		if (is_numeric($id)) {

			return $result->row();

		}



		return $result->result_array();

	}



	/**

		* { delete purchase contract attachment }

		*

		* @param         $id     The identifier

		*

		* @return     boolean  

	*/

	public function delete_pur_contract_attachment($id)
	{

		$attachment = $this->get_pur_contract_attachments('', $id);

		$deleted = false;

		if ($attachment) {

			if (empty($attachment->external)) {

				unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/' . $attachment->rel_id . '/' . $attachment->file_name);

			}

			$this->db->where('id', $attachment->id);

			$this->db->delete(db_prefix() . 'files');

			if ($this->db->affected_rows() > 0) {

				$deleted = true;

			}



			if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/' . $attachment->rel_id)) {

				// Check if no attachments left, so we can delete the folder also

				$other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/' . $attachment->rel_id);

				if (count($other_attachments) == 0) {

					// okey only index.html so we can delete the folder also

					delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_contract/' . $attachment->rel_id);

				}

			}

		}



		return $deleted;

	}



	/**

		* Adds a vendor category.

		*

		* @param         $data   The data

		*

		* @return     id inserted 

	*/

	public function add_vendor_category($data)
	{

		$this->db->insert(db_prefix() . 'pur_vendor_cate', $data);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {

			return $insert_id;

		}

		return false;

	}



	/**

		* { update vendor category }

		*

		* @param         $data   The data

		* @param        $id     The identifier

		*

		* @return     boolean   

	*/

	public function update_vendor_category($data, $id)
	{

		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_vendor_cate', $data);

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* { delete vendor category }

		*

		* @param         $id     The identifier

		*

		* @return     boolean  

	*/

	public function delete_vendor_category($id)
	{

		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_vendor_cate');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* Gets the vendor category.

		*

		* @param      string  $id     The identifier

		*

		* @return       The vendor category.

	*/

	public function get_vendor_category($id = '')
	{

		if ($id != '') {

			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'pur_vendor_cate')->row();

		} else {

			return $this->db->get(db_prefix() . 'pur_vendor_cate')->result_array();

		}

	}



	/**

		* Gets the purchase estimate attachments.

		*

		* @param        $id     The purchase estimate

		*

		* @return       The purchase estimate attachments.

	*/

	public function get_purchase_estimate_attachments($id)
	{



		$this->db->where('rel_id', $id);

		$this->db->where('rel_type', 'pur_estimate');

		return $this->db->get(db_prefix() . 'files')->result_array();

	}



	/**

		* Gets the purcahse estimate attachments.

		*

		* @param      <type>  $surope  The surope

		* @param      string  $id      The identifier

		*

		* @return     <type>  The part attachments.

	*/

	public function get_estimate_attachments($surope, $id = '')
	{

		// If is passed id get return only 1 attachment

		if (is_numeric($id)) {

			$this->db->where('id', $id);

		} else {

			$this->db->where('rel_id', $assets);

		}

		$this->db->where('rel_type', 'pur_estimate');

		$result = $this->db->get(db_prefix() . 'files');

		if (is_numeric($id)) {

			return $result->row();

		}



		return $result->result_array();

	}



	/**

		* { delete estimate attachment }

		*

		* @param         $id     The identifier

		*

		* @return     boolean 

	*/

	public function delete_estimate_attachment($id)
	{

		$attachment = $this->get_estimate_attachments('', $id);

		$deleted = false;

		if ($attachment) {

			if (empty($attachment->external)) {

				unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/' . $attachment->rel_id . '/' . $attachment->file_name);

			}

			$this->db->where('id', $attachment->id);

			$this->db->delete('tblfiles');

			if ($this->db->affected_rows() > 0) {

				$deleted = true;

			}



			if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/' . $attachment->rel_id)) {

				// Check if no attachments left, so we can delete the folder also

				$other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/' . $attachment->rel_id);

				if (count($other_attachments) == 0) {

					// okey only index.html so we can delete the folder also

					delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_estimate/' . $attachment->rel_id);

				}

			}

		}



		return $deleted;

	}



	/**

		* { update customfield po }

		*

		* @param        $id     The identifier

		* @param        $data   The data

	*/

	public function update_customfield_po($id, $data)
	{



		if (isset($data['custom_fields'])) {

			$custom_fields = $data['custom_fields'];

			if (handle_custom_fields_post($id, $custom_fields)) {

				return true;

			}

		}

		return false;

	}



	/**

		* { PO voucher pdf }

		*

		* @param        $po_voucher  The Purchase order voucher

		*

		* @return      ( pdf )

	*/

	public function povoucher_pdf($po_voucher)
	{

		return app_pdf('po_voucher', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Po_voucher_pdf'), $po_voucher);

	}



	/**

		* Gets the po voucher pdf html.

		*

		*

		*

		* @return     string  The request quotation pdf html.

	*/

	public function get_po_voucher_html()
	{

		$this->load->model('departments_model');



		$po_voucher = $this->db->get(db_prefix() . 'pur_orders')->result_array();





		$company_name = get_option('invoice_company_name');



		$address = get_option('invoice_company_address');

		$day = date('d');

		$month = date('m');

		$year = date('Y');





		$html = '<table class="table">

			<tbody>

			<tr>

            <td class="font_td_cpn">' . _l('purchase_company_name') . ': ' . $company_name . '</td>

            <td rowspan="2" width="" class="text-right">' . get_po_logo() . '</td>

			</tr>

			<tr>

            <td class="font_500">' . _l('address') . ': ' . $address . '</td>

			</tr>

			

			</tbody>

			</table>

			<table class="table">

			<tbody>

			<tr>

            

            <td class="td_ali_font"><h2 class="h2_style">' . mb_strtoupper(_l('po_voucher')) . '</h2></td>

			

			</tr>

			<tr>

            

            <td class="align_cen">' . _l('days') . ' ' . $day . ' ' . _l('month') . ' ' . $month . ' ' . _l('year') . ' ' . $year . '</td>

            

			</tr>

			

			</tbody>

			</table><br><br><br>';



		$html .= '<table class="table pur_request-item">

            <thead>

			<tr class="border_tr">

			<th align="left" class="thead-dark">' . _l('purchase_order') . '</th>

			<th  class="thead-dark">' . _l('date') . '</th>

			<th class="thead-dark">' . _l('type') . '</th>

			<th class="thead-dark">' . _l('project') . '</th>

			<th class="thead-dark">' . _l('department') . '</th>

			<th class="thead-dark">' . _l('vendor') . '</th>

			<th class="thead-dark">' . _l('approval_status') . '</th>

			<th class="thead-dark">' . _l('delivery_status') . '</th>

			<th class="thead-dark">' . _l('payment_status') . '</th>

			</tr>

            </thead>

			<tbody>';



		$tmn = 0;

		foreach ($po_voucher as $row) {

			$paid = $row['total'] - purorder_left_to_pay($row['id']);

			$percent = 0;

			if ($row['total'] > 0) {

				$percent = ($paid / $row['total']) * 100;

			}



			$delivery_status = '';

			if ($row['delivery_status'] == 0) {

				$delivery_status = _l('undelivered');

			} else {

				$delivery_status = _l('delivered');

			}



			$project_name = '';

			$department_name = '';

			$vendor_name = get_vendor_company_name($row['vendor']);



			$project = $this->projects_model->get($row['project']);

			$department = $this->departments_model->get($row['department']);

			if ($project) {

				$project_name = $project->name;

			}



			if ($department) {

				$department_name = $department->name;

			}



			$html .= '<tr>

				<td>' . $row['pur_order_number'] . '</td>

				<td>' . _d($row['order_date']) . '</td>

				<td>' . _l($row['type']) . '</td>

				<td>' . $project_name . '</td>

				<td>' . $department_name . '</td>

				<td>' . $vendor_name . '</td>

				<td>' . get_status_approve($row['approve_status']) . '</td>

				<td>' . $delivery_status . '</td>

				<td align="right">' . $percent . '%</td>

				</tr>';



		}

		$html .= '</tbody>

			</table><br><br>';





		$html .= '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_pdf.css') . '"  rel="stylesheet" type="text/css" />';

		return $html;

	}



	/**

		* Adds a pur invoice.

		*

		* @param        $data   The data

	*/

	public function add_pur_invoice($data)
	{

		$data['add_from'] = get_staff_user_id();

		$data['date_add'] = date('Y-m-d');

		$data['payment_status'] = 'unpaid';

		$prefix = get_purchase_option('pur_inv_prefix');



		$this->db->where('invoice_number', $data['invoice_number']);

		$check_exist_number = $this->db->get(db_prefix() . 'pur_invoices')->row();



		while ($check_exist_number) {

			$data['number'] = $data['number'] + 1;

			$data['invoice_number'] = $prefix . str_pad($data['number'], 5, '0', STR_PAD_LEFT);

			$this->db->where('invoice_number', $data['invoice_number']);

			$check_exist_number = $this->db->get(db_prefix() . 'pur_invoices')->row();

		}



		$data['invoice_date'] = to_sql_date($data['invoice_date']);

		$data['transaction_date'] = to_sql_date($data['transaction_date']);

		$data['subtotal'] = reformat_currency_pur($data['subtotal']);

		$data['tax'] = reformat_currency_pur($data['subtotal']);

		$data['total'] = reformat_currency_pur($data['total']);



		$tags = '';

		if (isset($data['tags'])) {

			$tags = $data['tags'];

			unset($data['tags']);

		}



		$this->db->insert(db_prefix() . 'pur_invoices', $data);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {

			$next_number = $data['number'] + 1;

			$this->db->where('option_name', 'next_inv_number');

			$this->db->update(db_prefix() . 'purchase_option', ['option_val' => $next_number,]);



			handle_tags_save($tags, $insert_id, 'pur_invoice');



			return $insert_id;

		}

		return false;

	}



	/**

		* { update pur invoice }

		*

		* @param        $id     The identifier

		* @param        $data   The data

	*/

	public function update_pur_invoice($id, $data)
	{

		$data['invoice_date'] = to_sql_date($data['invoice_date']);

		$data['transaction_date'] = to_sql_date($data['transaction_date']);

		$data['subtotal'] = reformat_currency_pur($data['subtotal']);

		$data['tax'] = reformat_currency_pur($data['subtotal']);

		$data['total'] = reformat_currency_pur($data['total']);



		if (isset($data['tags'])) {

			if (handle_tags_save($data['tags'], $id, 'pur_invoice')) {

				$affectedRows++;

			}

			unset($data['tags']);

		}



		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'pur_invoices', $data);

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* Gets the pur invoice.

		*

		* @param      string  $id     The identifier

		*

		* @return       The pur invoice.

	*/

	public function get_pur_invoice($id = '')
	{

		if ($id != '') {

			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'pur_invoices')->row();

		} else {

			return $this->db->get(db_prefix() . 'pur_invoices')->result_array();

		}

	}



	/**

		* { delete pur invoice }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean  

	*/

	public function delete_pur_invoice($id)
	{

		$this->db->where('rel_type', 'pur_invoice');

		$this->db->where('rel_id', $id);

		$this->db->delete(db_prefix() . 'taggables');



		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_invoices');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* Gets the payment invoice.

		*

		* @param        $invoice  The invoice

		*

		* @return       The payment invoice.

	*/

	public function get_payment_invoice($invoice)
	{

		$this->db->where('pur_invoice', $invoice);

		return $this->db->get(db_prefix() . 'pur_invoice_payment')->result_array();

	}



	/**

		* Adds a invoice payment.

		*

		* @param         $data       The data

		* @param         $invoice  The invoice id

		*

		* @return     boolean  

	*/

	public function add_invoice_payment($data, $invoice)
	{

		$data['date'] = to_sql_date($data['date']);

		$data['daterecorded'] = date('Y-m-d H:i:s');

		$data['amount'] = str_replace(',', '', $data['amount']);

		$data['pur_invoice'] = $invoice;

		$data['approval_status'] = 1;

		$data['requester'] = get_staff_user_id();

		$check_appr = $this->get_approve_setting('payment_request');

		if ($check_appr && $check_appr != false) {

			$data['approval_status'] = 1;

		} else {

			$data['approval_status'] = 2;

		}



		$this->db->insert(db_prefix() . 'pur_invoice_payment', $data);

		$insert_id = $this->db->insert_id();

		if ($insert_id) {



			if ($data['approval_status'] == 2) {

				$pur_invoice = $this->get_pur_invoice($invoice);

				if ($pur_invoice) {

					$status_inv = $pur_invoice->payment_status;

					if (purinvoice_left_to_pay($invoice) > 0) {

						$status_inv = 'partially_paid';

					} else {

						$status_inv = 'paid';

					}

					$this->db->where('id', $invoice);

					$this->db->update(db_prefix() . 'pur_invoices', ['payment_status' => $status_inv,]);

				}

			}



			return $insert_id;

		}

		return false;

	}



	/**

		* { delete invoice payment }

		*

		* @param      <type>   $id     The identifier

		*

		* @return     boolean  ( delete payment )

	*/

	public function delete_payment_pur_invoice($id)
	{

		$this->db->where('id', $id);

		$this->db->delete(db_prefix() . 'pur_invoice_payment');

		if ($this->db->affected_rows() > 0) {

			return true;

		}

		return false;

	}



	/**

		* Gets the payment pur invoice.

		*

		* @param      string  $id     The identifier

	*/

	public function get_payment_pur_invoice($id = '')
	{

		if ($id != '') {

			$this->db->where('id', $id);

			return $this->db->get(db_prefix() . 'pur_invoice_payment')->row();

		} else {

			return $this->db->get(db_prefix() . 'pur_invoice_payment')->result_array();

		}

	}



	/**

		* { update invoice after approve }

		*

		* @param        $id     The identifier

	*/

	public function update_invoice_after_approve($id)
	{

		$payment = $this->get_payment_pur_invoice($id);



		if ($payment) {

			$pur_invoice = $this->get_pur_invoice($payment->pur_invoice);

			if ($pur_invoice) {

				$status_inv = $pur_invoice->payment_status;

				if (purinvoice_left_to_pay($payment->pur_invoice) > 0) {

					$status_inv = 'partially_paid';

				} else {

					$status_inv = 'paid';

				}

				$this->db->where('id', $payment->pur_invoice);

				$this->db->update(db_prefix() . 'pur_invoices', ['payment_status' => $status_inv,]);

			}

		}

	}



	/**

		* Gets the purchase order attachments.

		*

		* @param      <type>  $id     The purchase order

		*

		* @return     <type>  The purchase order attachments.

	*/

	public function get_purchase_invoice_attachments($id)
	{



		$this->db->where('rel_id', $id);

		$this->db->where('rel_type', 'pur_invoice');

		return $this->db->get(db_prefix() . 'files')->result_array();

	}



	/**

		* Gets the inv attachments.

		*

		* @param      <type>  $surope  The surope

		* @param      string  $id      The identifier

		*

		* @return     <type>  The part attachments.

	*/

	public function get_purinv_attachments($surope, $id = '')
	{

		// If is passed id get return only 1 attachment

		if (is_numeric($id)) {

			$this->db->where('id', $id);

		} else {

			$this->db->where('rel_id', $assets);

		}

		$this->db->where('rel_type', 'pur_invoice');

		$result = $this->db->get(db_prefix() . 'files');

		if (is_numeric($id)) {

			return $result->row();

		}



		return $result->result_array();

	}



	/**

		* { delete purchase invoice attachment }

		*

		* @param         $id     The identifier

		*

		* @return     boolean 

	*/

	public function delete_purinv_attachment($id)
	{

		$attachment = $this->get_purinv_attachments('', $id);

		$deleted = false;

		if ($attachment) {

			if (empty($attachment->external)) {

				unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_invoice/' . $attachment->rel_id . '/' . $attachment->file_name);

			}

			$this->db->where('id', $attachment->id);

			$this->db->delete('tblfiles');

			if ($this->db->affected_rows() > 0) {

				$deleted = true;

			}



			if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_invoice/' . $attachment->rel_id)) {

				// Check if no attachments left, so we can delete the folder also

				$other_attachments = list_files(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_invoice/' . $attachment->rel_id);

				if (count($other_attachments) == 0) {

					// okey only index.html so we can delete the folder also

					delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/pur_invoice/' . $attachment->rel_id);

				}

			}

		}



		return $deleted;

	}



	/**

		* Gets the payment by contract.

		*

		* @param        $id     The identifier

	*/

	public function get_payment_by_contract($id)
	{

		return $this->db->query('select * from ' . db_prefix() . 'pur_invoice_payment where pur_invoice IN ( select id from ' . db_prefix() . 'pur_invoices where contract = ' . $id . ' )')->result_array();

	}



	/**

		* { purestimate pdf }

		*

		* @param        $pur_request  The pur request

		*

		* @return       ( purorder pdf )

	*/

	public function purestimate_pdf($pur_estimate, $id)
	{

		return app_pdf('pur_estimate', module_dir_path(PURCHASE_MODULE_NAME, 'libraries/pdf/Pur_estimate_pdf'), $pur_estimate, $id);

	}





	/**

		* Gets the pur request pdf html.

		*

		* @param      <type>  $pur_request_id  The pur request identifier

		*

		* @return     string  The pur request pdf html.

	*/

	public function get_purestimate_pdf_html($pur_estimate_id)
	{





		$pur_estimate = $this->get_estimate($pur_estimate_id);

		$pur_estimate_detail = $this->get_pur_estimate_detail($pur_estimate_id);

		$company_name = get_option('invoice_company_name');



		$address = get_option('invoice_company_address');

		$day = date('d', strtotime($pur_estimate->date));

		$month = date('m', strtotime($pur_estimate->date));

		$year = date('Y', strtotime($pur_estimate->date));



		$html = '<table class="table">

			<tbody>

			<tr>

            <td class="font_td_cpn">' . _l('purchase_company_name') . ': ' . $company_name . '</td>

            <td rowspan="2" width="" class="text-right">' . get_po_logo() . '</td>

            

			</tr>

			<tr>

            <td class="font_500">' . _l('address') . ': ' . $address . '</td>

            <td></td>

            

			</tr>

			</tbody>

			</table>

			<table class="table">

			<tbody>

			<tr>

            

            <td class="td_ali_font"><h2 class="h2_style">' . mb_strtoupper(_l('estimate')) . '</h2></td>

			

			</tr>

			<tr>

            

            <td class="align_cen">' . _l('days') . ' ' . $day . ' ' . _l('month') . ' ' . $month . ' ' . _l('year') . ' ' . $year . '</td>

            

			</tr>

			

			</tbody>

			</table>

			<table class="table">

			<tbody>

			<tr>

            <td class="td_width_25"><h4>' . _l('add_from') . ':</h4></td>

            <td class="td_width_75">' . get_staff_full_name($pur_estimate->addedfrom) . '</td>

			</tr>

			<tr>

            <td class="td_width_25"><h4>' . _l('vendor') . ':</h4></td>

            <td class="td_width_75">' . get_vendor_company_name($pur_estimate->vendor->userid) . '</td>

			</tr>

			

			</tbody>

			</table>

			

			<h3>

			' . html_entity_decode(format_pur_estimate_number($pur_estimate_id)) . '

			</h3>

			<br><br>

			';



		$html .= '<table class="table purorder-item">

			<thead>

			<tr>

            <th class="thead-dark">' . _l('items') . '</th>

            <th class="thead-dark" align="right">' . _l('purchase_unit_price') . '</th>

            <th class="thead-dark" align="right">' . _l('purchase_quantity') . '</th>

			

            <th class="thead-dark" align="right">' . _l('tax') . '</th>

			

            <th class="thead-dark" align="right">' . _l('discount') . '</th>

            <th class="thead-dark" align="right">' . _l('total') . '</th>

			</tr>

			</thead>

			<tbody>';

		$t_mn = 0;

		foreach ($pur_estimate_detail as $row) {

			$items = $this->get_items_by_id($row['ItemID']);

			$units = $this->get_units_by_id($row['unit_id']);

			$html .= '<tr nobr="true" class="sortable">

				<td >' . $items->commodity_code . ' - ' . $items->description . '</td>

				<td align="right">' . app_format_money($row['unit_price'], '') . '</td>

				<td align="right">' . $row['quantity'] . '</td>

				

				<td align="right">' . app_format_money($row['total'] - $row['into_money'], '') . '</td>

				

				<td align="right">' . app_format_money($row['discount_money'], '') . '</td>

				<td align="right">' . app_format_money($row['total_money'], '') . '</td>

				</tr>';



			$t_mn += $row['total_money'];

		}

		$html .= '</tbody>

			</table><br><br>';



		$html .= '<table class="table text-right"><tbody>';

		if ($pur_estimate->discount_total > 0) {

			$html .= '<tr id="subtotal">

				<td width="33%"></td>

				<td>' . _l('subtotal') . ' </td>

				<td class="subtotal">

				' . app_format_money($t_mn, '') . '

				</td>

				</tr>

				<tr id="subtotal">

				<td width="33%"></td>

				<td>' . _l('discount(%)') . '(%)' . '</td>

				<td class="subtotal">

				' . app_format_money($pur_estimate->discount_percent, '') . ' %' . '

				</td>

				</tr>

				<tr id="subtotal">

				<td width="33%"></td>

				<td>' . _l('discount(money)') . '</td>

				<td class="subtotal">

				' . app_format_money($pur_estimate->discount_total, '') . '

				</td>

				</tr>';

		}

		$html .= '<tr id="subtotal">

			<td width="33%"></td>

			<td>' . _l('total') . '</td>

			<td class="subtotal">

			' . app_format_money($pur_estimate->total, '') . '

			</td>

			</tr>';



		$html .= ' </tbody></table>';



		$html .= '<div class="col-md-12 mtop15">

			<h4>' . _l('terms_and_conditions') . ': </h4><p>' . html_entity_decode($pur_estimate->terms) . '</p>

			

			</div>';

		$html .= '<br>

			<br>

			<br>

			<br>';

		$html .= '<link href="' . module_dir_url(PURCHASE_MODULE_NAME, 'assets/css/pur_order_pdf.css') . '"  rel="stylesheet" type="text/css" />';

		return $html;

	}



	/**

		* Sends a quotation.

		*

		* @param         $data   The data

		*

		* @return     boolean

	*/

	public function send_quotation($data)
	{

		$staff_id = get_staff_user_id();



		$inbox = array();



		$inbox['to'] = implode(',', $data['email']);

		$inbox['sender_name'] = get_staff_full_name($staff_id);

		$inbox['subject'] = _strip_tags($data['subject']);

		$inbox['body'] = _strip_tags($data['content']);

		$inbox['body'] = nl2br_save_html($inbox['body']);

		$inbox['date_received'] = date('Y-m-d H:i:s');

		$inbox['from_email'] = get_option('smtp_email');



		if (strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0) {



			$ci = &get_instance();

			$ci->email->initialize();

			$ci->load->library('email');

			$ci->email->clear(true);

			$ci->email->from($inbox['from_email'], $inbox['sender_name']);

			$ci->email->to($inbox['to']);



			$ci->email->subject($inbox['subject']);

			$ci->email->message($inbox['body']);



			$attachment_url = site_url(PURCHASE_PATH . 'send_quotation/' . $data['pur_estimate_id'] . '/' . str_replace(" ", "_", $_FILES['attachment']['name']));

			$ci->email->attach($attachment_url);

			return $ci->email->send(true);

		}



		return false;

	}



	/**

		* Sends a purchase order.

		*

		* @param         $data   The data

		*

		* @return     boolean

	*/

	public function send_po($data)
	{

		$staff_id = get_staff_user_id();



		$inbox = array();



		$inbox['to'] = implode(',', $data['email']);

		$inbox['sender_name'] = get_staff_full_name($staff_id);

		$inbox['subject'] = _strip_tags($data['subject']);

		$inbox['body'] = _strip_tags($data['content']);

		$inbox['body'] = nl2br_save_html($inbox['body']);

		$inbox['date_received'] = date('Y-m-d H:i:s');

		$inbox['from_email'] = get_option('smtp_email');



		if (strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0) {



			$ci = &get_instance();

			$ci->email->initialize();

			$ci->load->library('email');

			$ci->email->clear(true);

			$ci->email->from($inbox['from_email'], $inbox['sender_name']);

			$ci->email->to($inbox['to']);



			$ci->email->subject($inbox['subject']);

			$ci->email->message($inbox['body']);



			$attachment_url = site_url(PURCHASE_PATH . 'send_po/' . $data['po_id'] . '/' . str_replace(" ", "_", $_FILES['attachment']['name']));

			$ci->email->attach($attachment_url);

			return $ci->email->send(true);

		}



		return false;

	}



	/**

		* import xlsx commodity

		* @param  array $data

		* @return integer

	*/

	public function import_xlsx_commodity($data)
	{

		if ($data['commodity_barcode'] != '') {

			$data['commodity_barcode'] = $data['commodity_barcode'];

		} else {

			$data['commodity_barcode'] = $this->generate_commodity_barcode();

		}





		/*create sku code*/

		if ($data['sku_code'] != '') {

			$data['sku_code'] = str_replace(' ', '', $data['sku_code']);

		} else {

			//data sku_code = group_character.sub_code.commodity_str_betwen.next_commodity_id; // X_X_000.id auto increment

			$data['sku_code'] = $this->create_sku_code($data['DivisionID'], isset($data['sub_group']) ? $data['sub_group'] : '');

			/*create sku code*/

		}



		if (get_warehouse_option('barcode_with_sku_code') == 1) {

			$data['commodity_barcode'] = $data['sku_code'];

		}



		/*check update*/



		$item = $this->db->query('select * from tblitems where commodity_code = "' . $data['commodity_code'] . '"')->row();



		if ($item) {

			//check sku code dulicate

			if ($this->check_sku_duplicate(['sku_code' => $data['sku_code'], 'item_id' => $item->id]) == false) {

				return false;

			}



			if (isset($data['tags'])) {

				$tags_value = $data['tags'];

				unset($data['tags']);

			} else {

				$tags_value = '';

			}



			foreach ($data as $key => $data_value) {

				if (!isset($data_value)) {

					unset($data[$key]);

				}

			}



			$minimum_inventory = 0;

			if (isset($data['minimum_inventory'])) {

				$minimum_inventory = $data['minimum_inventory'];

				unset($data['minimum_inventory']);

			}



			//update

			$this->db->where('commodity_code', $data['commodity_code']);

			$this->db->update(db_prefix() . 'items', $data);



			if ($this->db->affected_rows() > 0) {

				return true;

			}

		} else {

			//check sku code dulicate

			if ($this->check_sku_duplicate(['sku_code' => $data['sku_code'], 'item_id' => '']) == false) {

				return false;

			}



			$sku_prefix = '';



			if (function_exists('get_warehouse_option')) {

				$sku_prefix = get_warehouse_option('item_sku_prefix');

			}



			$data['sku_code'] = $sku_prefix . $data['sku_code'];



			//insert

			$this->db->insert(db_prefix() . 'items', $data);

			$insert_id = $this->db->insert_id();



			return $insert_id;

		}

	}



	/**

		* check sku duplicate

		* @param  [type] $data 

		* @return [type]       

	*/

	public function check_sku_duplicate($data)
	{

		if (isset($data['item_id'])) {

			//check update

			$this->db->where('sku_code', $data['sku_code']);

			$this->db->where('id != ', $data['item_id']);



			$items = $this->db->get(db_prefix() . 'items')->result_array();



			if (count($items) > 0) {

				return false;

			}

			return true;



		} elseif (isset($data['sku_code'])) {

			//check insert

			$this->db->where('sku_code', $data['sku_code']);

			$items = $this->db->get(db_prefix() . 'items')->row();

			if ($items) {

				return false;

			}

			return true;

		}



		return true;



	}



	public function remove_po_logo()
	{



		$this->db->where('rel_id', 0);

		$this->db->where('rel_type', 'po_logo');

		$avar = $this->db->get(db_prefix() . 'files')->row();



		if ($avar) {

			if (empty($avar->external)) {

				unlink(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id . '/' . $avar->file_name);

			}

			$this->db->where('id', $avar->id);

			$this->db->delete('tblfiles');



			if (is_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id)) {

				// Check if no avars left, so we can delete the folder also

				$other_avars = list_files(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id);

				if (count($other_avars) == 0) {

					// okey only index.html so we can delete the folder also

					delete_dir(PURCHASE_MODULE_UPLOAD_FOLDER . '/po_logo/' . $avar->rel_id);

				}

			}

		}



		return true;

	}

	public function get_Order_list()
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'purchasemaster');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasemaster.AccountID', 'left');

		//  $this->db->where(db_prefix() . 'clients.userid', $id);

		$this->db->where(db_prefix() . 'purchasemaster.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'purchasemaster.FY', $year);

		$this->db->order_by(db_prefix() . 'purchasemaster.PurchID', "DESC");

		return $this->db->get()->result_array();

	}



	public function load_data_for_purchase($data)
	{

		$from_date = to_sql_date($data["from_date"]);

		$to_date = to_sql_date($data["to_date"]);

		$status = $data["status"];

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'purchasemaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'purchasemaster.FY = "' . $fy . '" AND ' . db_prefix() . 'purchasemaster.PlantID = "' . $selected_company . '" ';

		if (!empty($status)) {

			$sql1 .= ' AND ' . db_prefix() . 'purchasemaster.cur_status = "' . $status . '"';

		}

		$sql1 .= ' ORDER BY Transdate DESC';

		$sql = 'SELECT ' . db_prefix() . 'purchasemaster.*,  

			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasemaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . $selected_company . ') as AccountName,

			(SELECT COALESCE(remark,"") FROM ' . db_prefix() . 'ItemWiseQCStatus WHERE ' . db_prefix() . 'ItemWiseQCStatus.PurchaseEntryNo = ' . db_prefix() . 'purchasemaster.PurchID  LIMIT 1) as remark

			FROM ' . db_prefix() . 'purchasemaster WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();



		foreach ($result as &$each) {

			$each['QCStatus'] = $this->GetItemWiseQCStatusByEntryNo($each['PurchID']);

		}

		return $result;

	}

	public function get_unique_purchasemaster($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $this->session->userdata('finacial_year');

		$this->db->select('tblpurchasemaster.*,tblpurchasemaster.AccountID As Vendor,tblclients.*,tblxx_statelist.*,tblaccountbalances.*,tblxx_citylist.city_name,tblTDSMaster.TDSName,tblTDSMaster.TDSCode');

		$this->db->from(db_prefix() . 'purchasemaster');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasemaster.AccountID', 'left');

		$this->db->join(db_prefix() . 'TDSMaster', db_prefix() . 'TDSMaster.TDSCode = ' . db_prefix() . 'clients.TDSSection', 'left');

		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');

		$this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'xx_citylist.id = ' . db_prefix() . 'clients.city', 'left');

		$this->db->join(db_prefix() . 'accountbalances', db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND ' . db_prefix() . 'accountbalances.FY ="' . $year . '"', 'left');

		$this->db->where(db_prefix() . 'purchasemaster.PurchID', $id);

		$this->db->where(db_prefix() . 'purchasemaster.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'purchasemaster.FY', $year);

		$data = $this->db->get()->row();

		if ($data) {

			$data->items = $this->ItemAssocToVendor($data->AccountID);

		}

		return $data;

	}



	public function get_unique_history($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID', 'left');

		// $this->db->join(db_prefix() . 'history', db_prefix() . 'history.OrderID = ' . db_prefix() . 'purchasemaster.PurchID', 'left');

		$this->db->where(db_prefix() . 'history.OrderID', $id);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		//$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		return $this->db->get()->result_array();

	}





	public function update_purchase_order($data, $id)
	{

		$selected_company = $this->session->userdata('root_company');



		$GodownID = $data['GodownID'];

		$fy = $this->session->userdata('finacial_year');

		$totalPurchAmt = 0;

		$totalDISC = 0;

		$totalcgst = 0;

		$totalsgst = 0;

		$totaligst = 0;

		$totalInvAmt1 = 0;

		if (isset($data['pur_order_detail'])) {

			$pur_order_detail = json_decode($data['pur_order_detail']);

			unset($data['pur_order_detail']);

			$es_detail = [];

			$row = [];

			$rq_val = [];

			$header = [];

			$header[] = 'ItemID';

			$header[] = 'description';

			$header[] = 'pur_unit';

			$header[] = 'CaseQty';

			$header[] = 'QTY';

			$header[] = 'Cases';

			$header[] = 'PurchRate';

			$header[] = 'disc';

			$header[] = 'DiscAmt';

			$header[] = 'batch_no';

			$header[] = 'mfg_date';

			$header[] = 'expiry_date';

			$header[] = 'GST';

			$header[] = 'CGSTAMT';

			$header[] = 'SGSTAMT';

			$header[] = 'IGSTAMT';

			$header[] = 'total_money';





			foreach ($pur_order_detail as $key => $value) {



				if ($value[0] != '') {

					$es_detail[] = array_combine($header, $value);

					$totalPurchAmt += $value[4] * $value[6];

					$totalDISC += $value[8];

					$totalcgst += $value[13];

					$totalsgst += $value[14];

					$totaligst += $value[15];

					$totalInvAmt1 += ($value[4] * $value[6]) - $value[8] + $value[13] + $value[14] + $value[15];

				}

			}

		}



		if (isset($data['charges_details'])) {

			$charges_details = json_decode($data['charges_details']);

			unset($data['charges_details']);

			$charges_detail = [];

			$header2 = [];

			$header2[] = 'AccountID';

			$header2[] = 'AccountName';

			$header2[] = 'hsn';

			$header2[] = 'qty';

			$header2[] = 'rate';

			$header2[] = 'Gst';

			$header2[] = 'CGst';

			$header2[] = 'SGst';

			$header2[] = 'IGst';

			$header2[] = 'NetAmt';

			$header2[] = 'Remark';

			$ChrTaxableAmt = 0;

			$ChrSGSTAmt = 0;

			$ChrCGSTAmt = 0;

			$ChrIGSTAmt = 0;

			$ChrInvoiceAmt = 0;

			$totalInvAmt2 = 0;

			foreach ($charges_details as $key2 => $value2) {

				if ($value2[0] != '') {

					$charges_detail[] = array_combine($header2, $value2);

					$ChrTaxableAmt += $value2[3] * $value2[4];

					$ChrSGSTAmt += $value2[7];

					$ChrCGSTAmt += $value2[6];

					$ChrIGSTAmt += $value2[8];

					$ChrInvoiceAmt += $value2[9];



					$totalInvAmt2 += $value2[9];

				}

			}

		}





		// echo $totalInvAmt;die;

		$old_pur_details = $this->purchase_model->get_purchase_detail($id);

		$AccountID = $data['vendor'];

		$back_ItCount = $this->db->select('*')->get_where(db_prefix() . 'purchasemaster', array('PurchID' => $id, 'PlantID' => $selected_company))->row();

		if ($data['status'] == 'Approve') {

			$data['status'] = 'Completed';

		}



		$QcStatus = $this->GetItemWisePendingQCStatusByEntryNo($id);



		$chkQC = true;

		if ($data['status'] == 'Completed') {

			if (count($QcStatus) > 0) {

				$chkQC = false;

			}

		}



		if ($chkQC == true) {

			// Add PurchaseMaster Audit record 

			$PurchaseAudit = array(

				"PlantID" => $back_ItCount->PlantID,

				"FY" => $back_ItCount->FY,

				"BT" => $back_ItCount->BT,

				"PurchID" => $back_ItCount->PurchID,

				"Transdate" => $back_ItCount->Transdate,

				"AccountID" => $back_ItCount->AccountID,

				"FrtAccountID" => $back_ItCount->FrtAccountID,

				"Invoiceno" => $back_ItCount->Invoiceno,

				"Invoicedate" => $back_ItCount->Invoicedate,

				"Purchamt" => $back_ItCount->Purchamt,

				"Discamt" => $back_ItCount->Discamt,

				"Frtamt" => $back_ItCount->Frtamt,

				"Othamt" => $back_ItCount->Othamt,

				"Invamt" => $back_ItCount->Invamt,

				"ItCount" => $back_ItCount->ItCount,

				"Userid" => $back_ItCount->Userid,

				"RoundOffAmt" => $back_ItCount->RoundOffAmt,

				"OthAccountID" => $back_ItCount->OthAccountID,

				"cgstamt" => $back_ItCount->cgstamt,

				"sgstamt" => $back_ItCount->sgstamt,

				"igstamt" => $back_ItCount->igstamt,

				"tcs" => $back_ItCount->tcs,

				"tcsAmt" => $back_ItCount->tcsAmt,

				"UserID2" => $this->session->userdata('username'),

				"Lupdate" => date('Y-m-d H:i:s'),

			);

			if ($this->db->insert(db_prefix() . 'purchasemaster_Audit', $PurchaseAudit)) {

				foreach ($old_pur_details as $key => $value) {

					$Item_audit = array(

						"PlantID" => $value['PlantID'],

						"FY" => $value['FY'],

						"OrderID" => $value['OrderID'],

						"BillID" => $value['BillID'],

						"TransID" => $value['TransID'],

						"IsSchemeYN" => $value['IsSchemeYN'],

						"TransDate" => $value['TransDate'],

						"TransDate2" => $value['TransDate2'],

						"TType" => $value['TType'],

						"TType2" => $value['TType2'],

						"AccountID" => $value['AccountID'],

						"ItemID" => $value['ItemID'],

						"GodownID" => $value['GodownID'],

						"PurchRate" => $value['PurchRate'],

						"Mrp" => $value['Mrp'],

						"SaleRate" => $value['SaleRate'],

						"SuppliedIn" => $value['SuppliedIn'],

						"OrderQty" => $value['OrderQty'],

						"eOrderQty" => $value['eOrderQty'],

						"ereason" => $value['ereason'],

						"BilledQty" => $value['BilledQty'],

						"DiscPerc" => $value['DiscPerc'],

						"DiscAmt" => $value['DiscAmt'],

						"cgst" => $value['cgst'],

						"cgstamt" => $value['cgstamt'],

						"sgst" => $value['sgst'],

						"sgstamt" => $value['sgstamt'],

						"igst" => $value['igst'],

						"igstamt" => $value['igstamt'],

						"CaseQty" => $value['CaseQty'],

						"Cases" => $value['Cases'],

						"OrderAmt" => $value['OrderAmt'],

						"ChallanAmt" => $value['ChallanAmt'],

						"NetOrderAmt" => $value['NetOrderAmt'],

						"NetChallanAmt" => $value['NetChallanAmt'],

						"Ordinalno" => $value['Ordinalno'],

						"rowid" => $value['rowid'],

						"UserID" => $value['UserID'],

						"cnfid" => $value['cnfid'],

						"UserID2" => $this->session->userdata('username'),

						"Lupdate" => date('Y-m-d H:i:s'),

					);

					$this->db->insert(db_prefix() . 'history_Audit', $Item_audit);

				}

			}

			$Discamt = 0;

			$ItCount = count($es_detail);

			$PurchID = $data['pur_order_number'];

			$old_date = $data['trans_date'];

			$new_date = to_sql_date($data['prd_date']) . " " . date('H:i:m');

			$Invoiceno = $data['invoce_n'];

			$invoce_date = to_sql_date($data['invoce_date']);

			$Discamt = $data['dc_total'];

			$cgstamt = str_replace(",", "", $data['CGST_amt']);

			$sgstamt = str_replace(",", "", $data['SGST_AMT']);

			$RoundOffAmt = $data['Round_OFF'];

			$igstamt = str_replace(",", "", $data['IGST_amt']);

			$Invamt = str_replace(",", "", $data['Invoice_amt']);

			$purchAmt = str_replace(",", "", $data['total_mn']);



			$TdsAmt = $data['TDS_amt'];

			$TdsRate = $data['TDSPer'];

			$TDSCode = $data['TDSCode'];



			$totalInvAmt = $totalInvAmt1 + $totalInvAmt2 + $TdsAmt;

			$data_array = array(

				'Transdate' => $new_date,

				'AccountID' => $AccountID,

				'cur_status' => $data['status'],

				'Purchamt' => $purchAmt - $ChrTaxableAmt,

				'OtherCharges' => $ChrTaxableAmt,

				'Discamt' => $Discamt,

				'Invamt' => $Invamt,

				'Invoiceno' => $Invoiceno,

				'Invoicedate' => $invoce_date,

				'ItCount' => $ItCount,

				'RoundOffAmt' => $RoundOffAmt,

				'cgstamt' => $cgstamt,

				'sgstamt' => $sgstamt,

				'igstamt' => $igstamt,

				'TdsAmt' => $TdsAmt,

				'TdsRate' => $TdsRate,

				'TdsSection' => $TDSCode,

				"Lupdate" => date('Y-m-d H:i:s'),

				"UserID2" => $this->session->userdata('username')

			);



			$this->db->where('PlantID', $selected_company);

			$this->db->LIKE('FY', $fy);

			$this->db->where('PurchID', $id);

			$this->db->update(db_prefix() . 'purchasemaster', $data_array);

			if ($this->db->affected_rows() > 0) {

				$transID = null;

				if ($data['status'] == 'Completed') {

					$GetReconsile = $this->GetReconsileEntry($PurchID);



					if (count($GetReconsile) > 0) {

						$Reconsile_Arr = array(

							"Amount" => $Invamt,

							"UserID" => $this->session->userdata('username')

						);

						$this->db->where('TransID', $PurchID);

						$this->db->update(db_prefix() . 'ReconsileMaster', $Reconsile_Arr);

					} else {

						$Reconsile_Arr = array(

							"TransDate" => $new_date,

							"AccountID" => $AccountID,

							"TransID" => $PurchID,

							"Amount" => $Invamt,

							"TType" => "CR",

							"PassedFrom" => "PURCHASE",

							"Status" => "N",

							"UserID" => $this->session->userdata('username')

						);

						$this->db->insert(db_prefix() . 'ReconsileMaster', $Reconsile_Arr);

					}



					$transID = $PurchID;

					$narrations = 'By Inv no.' . $Invoiceno . '-' . _d(to_sql_date($data['invoce_date'])) . '-' . $PurchID . '-' . _d(to_sql_date($data['prd_date']));



					// delete previous ledger entry



					$this->db->where('VoucherID', $PurchID);

					$this->db->delete(db_prefix() . 'accountledger');



					$ord_no = 1;

					// create new ledger entry

					$ledger_debit = array(

						"PlantID" => $selected_company,

						"Transdate" => $new_date,

						"TransDate2" => date('Y-m-d H:i:s'),

						"VoucherID" => $PurchID,

						"AccountID" => "PURCH",

						"EffectOn" => $AccountID,

						"TType" => "D",

						"Amount" => $totalPurchAmt,

						"Narration" => $narrations,

						"PassedFrom" => "PURCHASE",

						"OrdinalNo" => $ord_no,

						"UserID" => $_SESSION['username'],

						"FY" => $fy

					);

					$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

					$ord_no++;



					if ($totalDISC > 0) {



						// create new ledger entry for Discount Amt

						$ledger_credit = array(

							"PlantID" => $selected_company,

							"Transdate" => $new_date,

							"TransDate2" => date('Y-m-d H:i:s'),

							"VoucherID" => $PurchID,

							"AccountID" => "DISC",

							"EffectOn" => $AccountID,

							"TType" => "C",

							// "Amount" => $Discamt,

							"Amount" => $totalDISC,

							"Narration" => $narrations,

							"PassedFrom" => "PURCHASE",

							"OrdinalNo" => $ord_no,

							"UserID" => $_SESSION['username'],

							"FY" => $fy

						);

						$this->db->insert(db_prefix() . 'accountledger', $ledger_credit);

						$ord_no++;

					}





					// gst ledger update && Balance

					if ($totaligst > 0) {





						// create new ledger entry

						$ledger_debit = array(

							"PlantID" => $selected_company,

							"Transdate" => $new_date,

							"TransDate2" => date('Y-m-d H:i:s'),

							"VoucherID" => $PurchID,

							"AccountID" => "IGST",

							"EffectOn" => $AccountID,

							"TType" => "D",

							"Amount" => $totaligst,

							"Narration" => $narrations,

							"PassedFrom" => "PURCHASE",

							"OrdinalNo" => $ord_no,

							"UserID" => $_SESSION['username'],

							"FY" => $fy

						);

						$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

						$ord_no++;

					} else {



						// create new ledger entry

						// CGST

						$ledger_debit = array(

							"PlantID" => $selected_company,

							"Transdate" => $new_date,

							"TransDate2" => date('Y-m-d H:i:s'),

							"VoucherID" => $PurchID,

							"AccountID" => "CGST",

							"EffectOn" => $AccountID,

							"TType" => "D",

							"Amount" => $totalcgst,

							"Narration" => $narrations,

							"PassedFrom" => "PURCHASE",

							"OrdinalNo" => $ord_no,

							"UserID" => $_SESSION['username'],

							"FY" => $fy

						);

						$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

						$ord_no++;

						// create new ledger entry

						// SGST

						$ledger_debit = array(

							"PlantID" => $selected_company,

							"Transdate" => $new_date,

							"TransDate2" => date('Y-m-d H:i:s'),

							"VoucherID" => $PurchID,

							"AccountID" => "SGST",

							"EffectOn" => $AccountID,

							"TType" => "D",

							// "Amount" => $sgstamt,

							"Amount" => $totalsgst,

							"Narration" => $narrations,

							"PassedFrom" => "PURCHASE",

							"OrdinalNo" => $ord_no,

							"UserID" => $_SESSION['username'],

							"FY" => $fy

						);

						$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

						$ord_no++;



					}



					if ($TdsAmt > 0) {



						// create new ledger entry for TDS Amt

						$ledger_debit = array(

							"PlantID" => $selected_company,

							"Transdate" => $new_date,

							"TransDate2" => date('Y-m-d H:i:s'),

							"VoucherID" => $PurchID,

							"AccountID" => "TDS",

							"EffectOn" => $AccountID,

							"TType" => "D",

							"Amount" => $TdsAmt,

							"Narration" => $narrations,

							"PassedFrom" => "PURCHASE",

							"OrdinalNo" => $ord_no,

							"UserID" => $_SESSION['username'],

							"FY" => $fy

						);

						$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

						$ord_no++;

					}





					foreach ($charges_detail as $value2) {

						// Charges Account ledger debit 

						$ledger_debit = array(

							"PlantID" => $selected_company,

							"Transdate" => $new_date,

							"TransDate2" => date('Y-m-d H:i:s'),

							"VoucherID" => $id,

							"AccountID" => $value2['AccountID'],

							"EffectOn" => $value2['AccountID'],

							"TType" => "D",

							"Amount" => $value2['rate'] * $value2['qty'],

							"Narration" => $narrations,

							"PassedFrom" => "PURCHASE",

							"OrdinalNo" => $ord_n,

							"UserID" => $_SESSION['username'],

							"FY" => $fy

						);

						$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

						$ord_n++;

						// GST Account ledger debit as per Vendor state

						if ($value2['IGst'] > 0) {

							// IGST Ledger Debit

							$ledger_debit = array(

								"PlantID" => $selected_company,

								"Transdate" => $new_date,

								"TransDate2" => date('Y-m-d H:i:s'),

								"VoucherID" => $id,

								"AccountID" => "IGST",

								"EffectOn" => $value2['AccountID'],

								"TType" => "D",

								"Amount" => $value2['IGst'],

								"Narration" => $narrations,

								"PassedFrom" => "PURCHASE",

								"OrdinalNo" => $ord_n,

								"UserID" => $_SESSION['username'],

								"FY" => $fy

							);

							$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

							$ord_n++;

						} else {

							// CGST and SGST Account ledger Debit

							$ledger_debit = array(

								"PlantID" => $selected_company,

								"Transdate" => $new_date,

								"TransDate2" => date('Y-m-d H:i:s'),

								"VoucherID" => $id,

								"AccountID" => "CGST",

								"EffectOn" => $value2['AccountID'],

								"TType" => "D",

								"Amount" => $value2['CGst'],

								"Narration" => $narrations,

								"PassedFrom" => "PURCHASE",

								"OrdinalNo" => $ord_n,

								"UserID" => $_SESSION['username'],

								"FY" => $fy

							);

							$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

							$ord_n++;



							$ledger_debit = array(

								"PlantID" => $selected_company,

								"Transdate" => $new_date,

								"TransDate2" => date('Y-m-d H:i:s'),

								"VoucherID" => $id,

								"AccountID" => "SGST",

								"EffectOn" => $value2['AccountID'],

								"TType" => "D",

								"Amount" => $value2['SGst'],

								"Narration" => $narrations,

								"PassedFrom" => "PURCHASE",

								"OrdinalNo" => $ord_n,

								"UserID" => $_SESSION['username'],

								"FY" => $fy

							);

							$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);



						}



					}





					$RoundOffAmt = $totalInvAmt - round($totalInvAmt);

					// create new ledger entry

					$ledger_credit = array(

						"PlantID" => $selected_company,

						"Transdate" => $new_date,

						"TransDate2" => date('Y-m-d H:i:s'),

						"VoucherID" => $PurchID,

						"AccountID" => $AccountID,

						"EffectOn" => 'PURCH',

						"TType" => "C",

						"Amount" => round($totalInvAmt),

						"Narration" => $narrations,

						"PassedFrom" => "PURCHASE",

						"OrdinalNo" => $ord_no,

						"UserID" => $_SESSION['username'],

						"FY" => $fy

					);

					$this->db->insert(db_prefix() . 'accountledger', $ledger_credit);

					$ord_no++;

					// for Discount Amt



					if ($RoundOffAmt > 0 || $RoundOffAmt < 0) {

						if ($RoundOffAmt < 0) {

							$TType = "D";

						} else {

							$TType = "C";

						}

						$ledger_debit = array(

							"PlantID" => $selected_company,

							"Transdate" => $new_date,

							"TransDate2" => date('Y-m-d H:i:s'),

							"VoucherID" => $PurchID,

							"AccountID" => "ROUNDOFF",

							"EffectOn" => $AccountID,

							"TType" => $TType,

							"Amount" => $RoundOffAmt,

							"Narration" => $narrations,

							"PassedFrom" => "PURCHASE",

							"OrdinalNo" => $ord_no,

							"UserID" => $_SESSION['username'],

							"FY" => $fy

						);

						$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

						$ord_no++;

					}

				}

				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $fy);

				$this->db->where('AccountID', $data['vendor_code']);

				$this->db->where('OrderID', $PurchID);

				$this->db->delete(db_prefix() . 'history');



				$deleted_item = array();

				$new_items = array();

				foreach ($es_detail as $value) {

					$item_c = $this->db->get_where(db_prefix() . 'items', array('id' => $value['ItemID'], 'PlantID' => $selected_company))->row();

					array_push($new_items, $item_c->ItemID);

				}

				$old_ItemID = array();

				$old_pur_details = $this->purchase_model->get_purchase_detail($id);

				foreach ($old_pur_details as $key => $value) {

					array_push($old_ItemID, $value["ItemID"]);

					//check deleted item

					if (!in_array($value["ItemID"], $new_items)) {

						array_push($deleted_item, $value["ItemID"]);

					}

				}



				if ($data['status'] != 'Completed') {

					$this->db->where('PurchaseEntryNo', $id);

					$this->db->delete(db_prefix() . 'ItemWiseQCStatus');

				}





				$i = 1;

				$ChkStatus = true;

				foreach ($es_detail as $value) {

					$item_c = $this->db->get_where(db_prefix() . 'items', array('id' => $value['ItemID'], 'PlantID' => $selected_company))->row();

					$Po_item = $this->db->get_where(db_prefix() . 'history', array('ItemID' => $item_c->ItemID, 'OrderID' => $data['Po_number'], 'TType2' => 'Order'))->row();

					$PEntry_item = $this->SumEntryItemEdit($data['Po_number'], $item_c->ItemID, $PurchID);



					if (!empty($Po_item)) {

						$totalItem = $PEntry_item->BilledQty + $value['QTY'];

						if ($totalItem < $Po_item->BilledQty) {

							$ChkStatus = false;

						}

					}

					$gst_devide = 0;

					$gst_igst = 0;

					if ($data['state_f'] == 'UP') {

						$gst_devide = $value['GST'] / 2;

					} else {

						$gst_igst = $value['GST'];

					}



					$PrevPurch = $this->db

						->where('ItemID', $item_c->ItemID)

						->where('TType', 'P')

						->where('TType2', 'Purchase')

						->order_by('id', 'DESC')

						->get(db_prefix() . 'history')

						->row();



					if ($value['PurchRate'] > $PrevPurch->BasicRate) {

						// echo "ok";die;

						$this->db->select(db_prefix() . 'staff.*');

						$this->db->where(db_prefix() . 'staff.PlantID', $selected_company);

						$this->db->where(db_prefix() . 'staff.admin', '1');

						$AdminStaff = $this->db->get(db_prefix() . 'staff')->result_array();

						foreach ($AdminStaff as $admin) {

							// echo "ok";die;

							$Notification_msg = "Purchase Rate Is Greater Than Previous Purchase Of (" . $item_c->description . ")(" . $id . ")";

							$notification_data = [

								'description' => $Notification_msg,

								'touserid' => $admin['staffid'],

								'link' => 'purchase/EditPurchaseEntry/' . $id,

							];

							$notification_data['additional_data'] = serialize([

								'Purch No. ' . $id,

							]);



							if (add_notification($notification_data)) {

								pusher_trigger_notification($admin['staffid']);

							}

						}

					}



					if (in_array($item_c->ItemID, $old_ItemID)) {

						$data_array_result_update = array(

							'AccountID' => $AccountID,

							'TransID' => $transID,

							'TransDate' => $new_date,

							'CaseQty' => $value['CaseQty'],

							'PurchRate' => $value['PurchRate'],

							'SaleRate' => $value['PurchRate'],

							'BasicRate' => $value['PurchRate'],

							'SuppliedIn' => 1,

							'Cases' => $value['Cases'],

							'OrderQty' => $value['QTY'],

							'BilledQty' => $value['QTY'],

							'OrderAmt' => $value['PurchRate'] * $value['QTY'],

							"DiscPerc" => $value['disc'],

							'DiscAmt' => $value['DiscAmt'],

							'gst' => $value['GST'],

							'cgst' => $gst_devide,

							'sgst' => $gst_devide,

							'igst' => $gst_igst,

							'cgstamt' => $value['CGSTAMT'],

							'sgstamt' => $value['SGSTAMT'],

							'igstamt' => $value['IGSTAMT'],

							'OrderAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'],

							'ChallanAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'],

							'NetOrderAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'] + $value['CGSTAMT'] + $value['SGSTAMT'] + $value['IGSTAMT'],

							'NetChallanAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'] + $value['CGSTAMT'] + $value['SGSTAMT'] + $value['IGSTAMT'],

							'batch_no' => $value['batch_no'],

							'mfg_date' => $value['mfg_date'],

							'expiry_date' => $value['expiry_date'],

							'UserID2' => $_SESSION['username'],

							'Lupdate' => date('Y-m-d H:i:s')

						);

						$this->db->where('OrderID', $id);

						$this->db->where('ItemID', $item_c->ItemID);

						$this->db->where('PlantID', $selected_company);

						$this->db->LIKE('FY', $fy);

						$this->db->update(db_prefix() . 'history', $data_array_result_update);



						if ($data['status'] != 'Completed') {

							$ChkQcMaster = $this->ChkQCMasterByItemID($item_c->ItemID);



							if (!empty($ChkQcMaster) && $value['QTY'] > 0) {

								$QCArray = array(

									'PurchaseEntryNo' => $id,

									'ItemID' => $item_c->ItemID,

									'Status' => 'N',

									'TransDate' => $new_date,

									'UserID' => $_SESSION['username'],

								);



								$this->db->insert(db_prefix() . 'ItemWiseQCStatus', $QCArray);

							}

						}

					} else {



						$next_purchase_batch_number = get_option('next_purchase_batch_number');

						$data_array_result_add = array(

							'PlantID' => $selected_company,

							'FY' => $fy,

							'cnfid' => 1,

							'OrderID' => $PurchID,

							'BillID' => $data['Po_number'],

							'TransID' => $transID,

							'TransDate' => $new_date,

							'GodownID' => $GodownID,

							'TransDate2' => date('Y-m-d H:i:s'),

							'TType' => 'P',

							'TType2' => 'Hold',

							'AccountID' => $AccountID,

							'ItemID' => $item_c->ItemID,

							'CaseQty' => $value['CaseQty'],

							'PurchRate' => $value['PurchRate'],

							'SaleRate' => $value['PurchRate'],

							'BasicRate' => $value['PurchRate'],

							'SuppliedIn' => 1,

							'Cases' => $value['Cases'],

							'OrderQty' => $value['QTY'],

							'BilledQty' => $value['QTY'],

							'OrderAmt' => $value['PurchRate'] * $value['QTY'],

							"DiscPerc" => $value['disc'],

							'DiscAmt' => $value['DiscAmt'],

							'gst' => $value['GST'],

							'cgst' => $gst_devide,

							'sgst' => $gst_devide,

							'igst' => $gst_igst,

							'cgstamt' => $value['CGSTAMT'],

							'sgstamt' => $value['SGSTAMT'],

							'igstamt' => $value['IGSTAMT'],

							'OrderAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'],

							'ChallanAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'],

							'NetOrderAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'] + $value['CGSTAMT'] + $value['SGSTAMT'] + $value['IGSTAMT'],

							'NetChallanAmt' => ($value['PurchRate'] * $value['QTY']) - $value['DiscAmt'] + $value['CGSTAMT'] + $value['SGSTAMT'] + $value['IGSTAMT'],

							'batch_no' => $value['batch_no'],

							'mfg_date' => $value['mfg_date'],

							'expiry_date' => $value['expiry_date'],

							'internal_batch_no' => $next_purchase_batch_number,

							'Ordinalno' => $i,

							'UserID' => $_SESSION['username']

						);

						if ($this->db->insert(db_prefix() . 'history', $data_array_result_add)) {

							$this->next_purchase_batch_number();



							if ($data['status'] != 'Completed') {

								$ChkQcMaster = $this->ChkQCMasterByItemID($item_c->ItemID);



								if (!empty($ChkQcMaster) && $value['QTY'] > 0) {

									$QCArray = array(

										'PurchaseEntryNo' => $PurchID,

										'ItemID' => $item_c->ItemID,

										'Status' => 'N',

										'TransDate' => $new_date,

										'UserID' => $_SESSION['username'],

									);



									$this->db->insert(db_prefix() . 'ItemWiseQCStatus', $QCArray);

								}

							}

						}

					}

				}

				if ($data['status'] == 'Completed') {

					if (!empty($data['Po_number'])) {

						$this->db->where('PurchID', $data['Po_number']);

						$this->db->where('PlantID', $selected_company);

						$this->db->LIKE('FY', $fy);



						if ($ChkStatus == true) {

							$this->db->update(db_prefix() . 'purchaseordermaster', [

								'cur_status' => 'Completed',

							]);

						} else {

							$this->db->update(db_prefix() . 'purchaseordermaster', [

								'cur_status' => 'InProgress',

							]);

						}



					}

				}

				$this->db->where('PONumber', $id);

				$this->db->delete(db_prefix() . 'purchase_charges');

				foreach ($charges_detail as $value2) {



					$data_array_result = array(

						'PONumber' => $id,

						'AccountID' => $value2['AccountID'],

						'qty' => $value2['qty'],

						'rate' => $value2['rate'],

						'gst_per' => $value2['Gst'],

						'cgst' => $value2['CGst'],

						'sgst' => $value2['SGst'],

						'igst' => $value2['IGst'],

						'amount' => $value2['NetAmt'],

						'remark' => $value2['Remark'],

						'UserID2' => $_SESSION['username'],

						'TransDate' => $new_date,

						'TransDate2' => date('Y-m-d H:i:s'),

						'Lupdate' => date('Y-m-d H:i:s'),

					);

					$this->db->insert(db_prefix() . 'purchase_charges', $data_array_result);



				}



				if ($data['status'] == 'Completed') {

					$data_array_result_update = array(

						'TType' => 'P',

						'TType2' => 'Purchase',

						'UserID2' => $_SESSION['username'],

						'Lupdate' => date('Y-m-d H:i:s')

					);

					$this->db->where('OrderID', $id);

					$this->db->where('TType2', 'Hold');

					$this->db->where('PlantID', $selected_company);

					$this->db->LIKE('FY', $fy);

					$this->db->update(db_prefix() . 'history', $data_array_result_update);

				}

				return true;

			} else {

				return false;

			}

		} else {

			return 'QCNOTOK';

		}

	}



	function getaccounts($postData)
	{



		$response = array();

		$selected_company = $this->session->userdata('root_company');

		$where_clients = '';

		if (isset($postData['search'])) {



			$q = $postData['search'];



			$this->db->select(db_prefix() . 'clients.*,' . db_prefix() . 'xx_statelist.state_name');

			$where_clients .= '(company LIKE "%' . $q . '%" ESCAPE \'!\' OR StationName LIKE "%' . $q . '%" ESCAPE \'!\' OR address LIKE "%' . $q . '%" ESCAPE \'!\' OR Address3 LIKE "%' . $q . '%" ESCAPE \'!\') AND ' . db_prefix() . 'clients.active = 1 AND ' . db_prefix() . 'clients.SubActGroupID1 = 100023';

			$this->db->join(db_prefix() . 'xx_statelist', '' . db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state');

			$this->db->where($where_clients);



			$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);



			$records = $this->db->get(db_prefix() . 'clients')->result();



			foreach ($records as $row) {

				$response[] = array("label" => $row->company, "value" => $row->AccountID, "address" => $row->address, "address2" => $row->Address3, "state" => $row->state, "station" => $row->StationName, "gst" => $row->vat, "state_name" => $row->state_name);

			}



		}



		return $response;

	}



	function itemlist($postData)
	{



		$response = array();

		$selected_company = $this->session->userdata('root_company');

		$where_items = '';

		if (isset($postData['search'])) {



			$q = $postData['search'];

			$MainGroupID = $postData['MainGroupID'];

			$Subgroup = $postData['Subgroup'];

			$Subgroup2 = $postData['Subgroup2'];



			$this->db->select(db_prefix() . 'items.*');

			$where_items .= '(	ItemID LIKE "%' . $q . '%" ESCAPE \'!\' OR description LIKE "%' . $q . '%" ESCAPE \'!\' OR long_description LIKE "%' . $q . '%" ESCAPE \'!\') AND ' . db_prefix() . 'items.isactive = "Y" ';



			$this->db->where($where_items);



			if (!empty($MainGroupID)) {

				$this->db->where(db_prefix() . 'items.MainGrpID', $MainGroupID);

			}

			if (!empty($Subgroup)) {

				$this->db->where(db_prefix() . 'items.SubGrpID1', $Subgroup);

			}

			if (!empty($Subgroup2)) {

				$this->db->where(db_prefix() . 'items.SubGrpID2', $Subgroup2);

			}

			$this->db->where(db_prefix() . 'items.PlantID', $selected_company);



			$records = $this->db->get(db_prefix() . 'items')->result();



			foreach ($records as $row) {

				$response[] = array("label" => $row->description, "value" => $row->ItemID);

			}



		}



		return $response;

	}



	public function get_pre_ledger_amt($vendor_code, $pur_id)
	{



		$selected_company = $this->session->userdata('root_company');

		$year = $this->session->userdata('finacial_year');

		$this->db->select();

		$this->db->from(db_prefix() . 'accountledger');

		$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

		$this->db->LIKE(db_prefix() . 'accountledger.FY', $year);

		$this->db->where(db_prefix() . 'accountledger.AccountID', $vendor_code);

		$this->db->where(db_prefix() . 'accountledger.VoucherID', $pur_id);

		return $this->db->get()->row();

	}



	public function get_pre_ledger_amt_PURCH($vendor_code, $pur_id, $type)
	{



		$selected_company = $this->session->userdata('root_company');

		$year = $this->session->userdata('finacial_year');

		$this->db->select();

		$this->db->from(db_prefix() . 'accountledger');

		$this->db->where(db_prefix() . 'accountledger.PlantID', $selected_company);

		$this->db->LIKE(db_prefix() . 'accountledger.FY', $year);

		$this->db->LIKE(db_prefix() . 'accountledger.TType', $type);

		$this->db->where(db_prefix() . 'accountledger.AccountID', $vendor_code);

		$this->db->where(db_prefix() . 'accountledger.VoucherID', $pur_id);

		return $this->db->get()->row();

	}



	public function get_purchase_for_body_data($filterdata)
	{

		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$report_type = $filterdata["report_type"];

		$accountID = $filterdata["accountID"];

		$ItemID = $filterdata["ItemID"];

		$MainGroupID = $filterdata["MainGroupID"];

		$Subgroup = $filterdata["Subgroup"];

		$Subgroup2 = $filterdata["Subgroup2"];

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');

		$sql = '';

		if ($report_type == 1 || $report_type == 3) {

			$sql .= 'SELECT tblpurchasemaster.*,tblclients.company';

		} else if ($report_type == 2) {

			$sql .= 'SELECT SUM(Purchamt) as Purchamt,SUM(Discamt) as Discamt,SUM(cgstamt) as cgstamt,SUM(sgstamt) as sgstamt,SUM(igstamt) as igstamt,SUM(Invamt) as Invamt,SUM(RoundOffAmt) as RoundOffAmt,tblclients.company,tblclients.AccountID';

		}

		$sql .= ' FROM `tblpurchasemaster` 

			INNER JOIN tblclients ON tblclients.AccountID=tblpurchasemaster.AccountID AND tblclients.PlantID = tblpurchasemaster.PlantID

			WHERE tblpurchasemaster.PlantID = ' . $selected_company . ' AND tblpurchasemaster.FY = "' . $fy . '" AND tblpurchasemaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59"';

		if ($report_type == 3) {

			$sql .= ' AND tblpurchasemaster.AccountID ="' . $accountID . '"';

		}

		$sql .= ' AND tblpurchasemaster.cur_status ="Completed"';

		if ($report_type == 2) {

			$sql .= ' GROUP BY tblpurchasemaster.AccountID';

		}

		//$sql .= ' GROUP BY tblhistory.ItemID,tblhistory.TType,tblhistory.TType2';

		$sql .= ' ORDER BY tblclients.company ASC';

		if (empty($ItemID) && empty($MainGroupID) && empty($accountID)) {

			$result = $this->db->query($sql)->result_array();

		}



		if (!empty($ItemID) || !empty($MainGroupID)) {

			$sql2 = 'SELECT tblhistory.OrderID,tblhistory.ItemID,tblhistory.mfg_date,tblhistory.expiry_date,tblpurchasemaster.Transdate,tblpurchasemaster.Invoicedate,tblpurchasemaster.Invoiceno,tblhistory.PurchRate,SUM(tblhistory.BilledQty) as rcptqty,SUM(tblhistory.ChallanAmt) as amount,SUM(tblhistory.DiscAmt) as discamt,SUM(tblhistory.sgstamt) as sgstamt,SUM(tblhistory.cgstamt) as cgstamt,SUM(tblhistory.igstamt) as igstamt,SUM(tblhistory.ChallanAmt) as netamount,tblclients.company,tblhistory.AccountID,tblitems.description 

				FROM `tblhistory`

				INNER JOIN tblitems ON tblitems.ItemID=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID

				INNER JOIN tblclients ON tblclients.AccountID=tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID

				INNER JOIN tblpurchasemaster ON tblpurchasemaster.PurchID=tblhistory.OrderID AND tblpurchasemaster.PlantID = tblhistory.PlantID AND tblpurchasemaster.FY = tblhistory.FY 

				WHERE tblhistory.PlantID =' . $selected_company . ' AND tblhistory.FY = "' . $fy . '"';

			if (!empty($accountID)) {

				$sql2 .= ' AND tblhistory.AccountID = "' . $accountID . '"';

			}



			if (!empty($ItemID)) {

				$sql2 .= ' AND tblhistory.ItemID = "' . $ItemID . '"';

			}

			if (!empty($MainGroupID)) {

				$sql2 .= ' AND tblitems.MainGrpID = "' . $MainGroupID . '"';

			}

			if (!empty($Subgroup)) {

				$sql2 .= ' AND tblitems.SubGrpID1 = "' . $Subgroup . '"';

			}

			if (!empty($Subgroup2)) {

				$sql2 .= ' AND tblitems.SubGrpID2 = "' . $Subgroup2 . '"';

			}

			$sql2 .= ' AND tblhistory.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59" 

				GROUP BY tblhistory.ItemID,tblhistory.OrderID ORDER BY tblclients.company ASC';

			$result = $this->db->query($sql2)->result_array();

		}

		if (empty($ItemID) && empty($MainGroupID) && !empty($accountID)) {

			$sql3 = 'SELECT tblhistory.OrderID,tbltaxes.taxrate as taxname,tblpurchasemaster.Transdate,tblpurchasemaster.Invoicedate,tblpurchasemaster.Invoiceno,tblhistory.PurchRate,SUM(tblhistory.BilledQty) as rcptqty,SUM(tblhistory.ChallanAmt) as amount,SUM(tblhistory.DiscAmt) as discamt,SUM(tblhistory.sgstamt) as sgstamt,SUM(tblhistory.cgstamt) as cgstamt,SUM(tblhistory.igstamt) as igstamt,SUM(tblhistory.ChallanAmt) as netamount,tblitems.description,tblitems.case_qty,tblitems.tax,tblhistory.AccountID 

				FROM `tblhistory`

				INNER JOIN tblitems ON tblitems.ItemID=tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID

				INNER JOIN tbltaxes ON tbltaxes.id=tblitems.tax 

				INNER JOIN tblpurchasemaster ON tblpurchasemaster.PurchID=tblhistory.OrderID AND tblpurchasemaster.PlantID = tblhistory.PlantID AND tblpurchasemaster.FY = tblhistory.FY 

				WHERE tblhistory.PlantID =' . $selected_company . ' AND tblhistory.FY = "' . $fy . '" AND tblhistory.AccountID = "' . $accountID . '"';



			$sql3 .= ' AND tblhistory.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59" 

				GROUP BY tblhistory.ItemID,tblpurchasemaster.PurchID ORDER BY tblpurchasemaster.PurchID ASC';

			$result = $this->db->query($sql3)->result_array();

		}

		return $result;

	}

	public function get_company_detail()
	{



		$selected_company = $this->session->userdata('root_company');



		$sql = 'SELECT ' . db_prefix() . 'rootcompany.*

			FROM ' . db_prefix() . 'rootcompany WHERE id = ' . $selected_company;



		$result = $this->db->query($sql)->row();



		return $result;





	}



	public function get_account_details($AccountID)
	{

		$selected_company = $this->session->userdata('root_company');

		$sql = 'SELECT ' . db_prefix() . 'clients.*

			FROM ' . db_prefix() . 'clients WHERE AccountID = "' . $AccountID . '" AND PlantID = ' . $selected_company;



		$result = $this->db->query($sql)->row();

		return $result;



	}



	public function get_item_details($ItemID)
	{

		$selected_company = $this->session->userdata('root_company');

		$sql = 'SELECT ' . db_prefix() . 'items.*

			FROM ' . db_prefix() . 'items WHERE ItemID = "' . $ItemID . '" AND PlantID = ' . $selected_company;



		$result = $this->db->query($sql)->row();

		return $result;



	}

	public function GetGodownData()
	{

		$PlantID = $this->session->userdata('root_company');

		$this->db->where('PlantID', $PlantID);

		$this->db->order_by(db_prefix() . 'godownmaster.Type,' . db_prefix() . 'godownmaster.AccountName', 'ASC');

		return $this->db->get(db_prefix() . 'godownmaster')->result_array();

	}

	public function get_purchase_detail($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $this->session->userdata('finacial_year');

		$this->db->select(db_prefix() . 'history.*');

		$this->db->from(db_prefix() . 'history');

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		$this->db->where(db_prefix() . 'history.BillID', $id);

		return $this->db->get()->result_array();

	}

	public function get_purchase_detail_order($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $this->session->userdata('finacial_year');

		$this->db->select(db_prefix() . 'history.*');

		$this->db->from(db_prefix() . 'history');

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		$this->db->where(db_prefix() . 'history.OrderID', $id);

		$this->db->where(db_prefix() . 'history.TType', 'P');

		$this->db->where(db_prefix() . 'history.TType2', 'Order');

		return $this->db->get()->result_array();

	}



	public function get_account_data()
	{

		$selected_company = $this->session->userdata('root_company');





		$this->db->order_by('company', 'asc');

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		return $this->db->get(db_prefix() . 'clients')->result_array();

	}

	public function getallstate()
	{



		$this->db->where('country_id', '1');

		$this->db->order_by('state_name', 'ASE');

		return $this->db->get(db_prefix() . 'xx_statelist')->result_array();

	}

	public function getallstation()
	{



		$selected_company = $this->session->userdata('root_company');





		$this->db->select(db_prefix() . 'clients.StationName,' . db_prefix() . 'clients.userid');

		$this->db->order_by('StationName', 'asc');

		$this->db->group_by('StationName');

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'clients.StationName !=', '');

		return $this->db->get(db_prefix() . 'clients')->result_array();

	}

	public function getallroute()
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->where('PlantID', $selected_company);

		return $this->db->get(db_prefix() . 'route')->result_array();

	}

	public function get_groups($id = '')
	{

		$selected_company = $this->session->userdata('root_company');

		if (is_numeric($id)) {

			$this->db->where('id', $id);



			return $this->db->get(db_prefix() . 'customers_groups')->row();

		}

		$this->db->where('PlantID', $selected_company);

		$this->db->order_by('name', 'asc');



		return $this->db->get(db_prefix() . 'customers_groups')->result_array();

	}

	public function get_state()
	{

		$this->db->select('*');

		$this->db->where('country_id', '1');

		$this->db->from(db_prefix() . 'xx_statelist');

		$this->db->order_by('state_name', 'ASC');



		return $this->db->get()->result_array();

	}

	// public function table_data($data)
	// {

	// 	$SubActGroupID = array('100023');

	// 	$this->db->select('GROUP_CONCAT(QUOTE(SubActGroupID)) as SubActGroupIDs');

	// 	// $this->db->where_in(db_prefix() . 'accountgroupssub.SubActGroupID1', $SubActGroupID);
	// 	$this->db->where_in(db_prefix() . 'AccountSubGroup.SubActGroupID1', $SubActGroupID);


	// 	$this->db->where(db_prefix() . 'accountgroupssub.IsVendor', 'Y');

	// 	$Data = $this->db->get('tblAccountSubGroup2')->row();



	// 	$commaSeparatedSubActGroupIDs = $Data->SubActGroupIDs;





	// 	$states = $data['states'];

	// 	$status = $data['status'];

	// 	$selected_company = $this->session->userdata('root_company');



	// 	$SQL = '';

	// 	$SQL .= 'SELECT tblclients.AccountID as AccountID,tblclients.vat,tblAccountSubGroup2.SubActGroupName,

	// 		company,state,address,StationName,city,tblclients.active as actstatus,tblxx_statelist.state_name,tblclients.acc_name AS acc_name,tblclients.zip AS zip,tblclients.phonenumber AS phonenumber,tblclients.altphonenumber AS altphonenumber,tblcontacts.email AS email,tblclients.city AS city, (SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tblcustomers_groups WHERE tblcustomers_groups.id = tblclients.DistributorType) as customerGroups

	// 		FROM tblclients

	// 		LEFT JOIN tblxx_statelist ON tblxx_statelist.short_name = tblclients.state

	// 		INNER JOIN tblcontacts ON tblclients.PlantID =tblcontacts.PlantID AND tblclients.AccountID=tblcontacts.AccountID 

	// 		INNER JOIN tblAccountSubGroup2 ON tblAccountSubGroup2.SubActGroupID =tblclients.SubActGroupID';

	// 	$SQL .= ' WHERE `tblclients`.`PlantID` = ' . $selected_company . '

	// 		AND tblclients.SubActGroupID IN(' . $commaSeparatedSubActGroupIDs . ')';



	// 	if ($states != '') {

	// 		$SQL .= '  AND `tblclients`.`state` = "' . $states . '"';

	// 	}

	// 	if ($status != '') {

	// 		$SQL .= '  AND `tblclients`.`active` = ' . $status;

	// 	}

	// 	$SQL .= ' ORDER BY `tblclients`.`AccountID` ASC';

	// 	$query = $this->db->query($SQL);

	// 	return $query->result_array();

	// }



	public function items_change_purchaseId($item_id, $purchaseId)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];



		$this->db->select();

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'purchasemaster', db_prefix() . 'purchasemaster.PurchID = ' . db_prefix() . 'history.OrderID', 'left');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID', 'left');

		$this->db->join(db_prefix() . 'taxes', db_prefix() . 'taxes.id = ' . db_prefix() . 'items.tax', 'left');

		$this->db->where(db_prefix() . 'history.TType', 'P');

		$this->db->where(db_prefix() . 'history.ItemID', $item_id);

		$this->db->where(db_prefix() . 'history.OrderID', $purchaseId);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$purch_data = $this->db->get()->row_array();



		return $purch_data;

	}



	public function add_pur_return_order($data)
	{

		// echo '<pre>';print_r($data);die; 

		if (isset($data['pur_order_detail'])) {

			$pur_order_detail = json_decode($data['pur_order_detail']);

			// print_r($pur_order_detail);

			unset($data['pur_order_detail']);

			$es_detail = [];

			$row = [];

			$rq_val = [];

			$header = [];

			$header[] = 'ItemID';

			$header[] = 'description';

			$header[] = 'pack';

			$header[] = 'purchaseId';

			$header[] = 'Purchqty';

			$header[] = 'Purchrate';

			$header[] = 'RtnCases';

			$header[] = 'inuint';

			$header[] = 'Amount';

			$header[] = 'disc';

			$header[] = 'discount_money';

			$header[] = 'CGST';

			$header[] = 'SGST';

			$header[] = 'IGST';

			$header[] = 'NetAmount';



			foreach ($pur_order_detail as $key => $value) {

				if ($value[0] != '') {

					$es_detail[] = array_combine($header, $value);

				}

			}

		}



		$acc_id = $this->db->select('AccountID')->get_where(db_prefix() . 'clients', array('userid' => $data['vendor']))->row();



		$PlantID = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');

		if ($PlantID == 1) {

			$purchaseRtn_orderNumbar = get_option('next_purchasertn_number_for_cspl');

			$GodownID = 'CSPL';

		} elseif ($PlantID == 2) {

			$purchaseRtn_orderNumbar = get_option('next_purchasertn_number_for_cff');

			$GodownID = 'CFF';

		} elseif ($PlantID == 3) {

			$purchaseRtn_orderNumbar = get_option('next_purchasertn_number_for_cbu');

			$GodownID = 'CBUPL';

		}

		$new_purchaseRtn_orderNumbar = 'PRT' . $FY . $purchaseRtn_orderNumbar;

		$ItCount = count($es_detail);

		$Transdate = to_sql_date($data['purch_rtn_date']) . " " . date('H:i:s');



		$vendor = $data['vendor'];

		$FrtAccountID = $data['Freight_1'];

		$OthAccountID = $data['Other_ac'];

		$Discamt = $data['dc_total'];

		$Frtamt = $data['Freight_AMT'];

		$cgstamt = $data['CGST_amt'];

		$Othamt = $data['Other_amt'];

		$sgstamt = $data['SGST_AMT'];

		$RoundOffAmt = $data['Round_OFF'];

		$igstamt = $data['IGST_amt'];

		$BillID = $data['purchase_id_store'];



		$Invamt = str_replace(",", "", $data['Invoice_amt']);

		$purchase_amt = str_replace(",", "", $data['total_mn']);

		$data_array = array(

			'PlantID' => $PlantID,

			'FY' => $FY,

			'BT' => 'Y',

			'PurchRtnID' => $new_purchaseRtn_orderNumbar,

			'Transdate' => $Transdate,

			'FrtAccountID' => $FrtAccountID,

			'AccountID' => $acc_id->AccountID,

			'Purchamt' => $purchase_amt,

			'Discamt' => $Discamt,

			'Frtamt' => $Frtamt,

			'Othamt' => $Othamt,

			'Invamt' => $Invamt,

			'ItCount' => $ItCount,

			'RoundOffAmt' => $RoundOffAmt,

			'OthAccountID' => $OthAccountID,

			'cgstamt' => $cgstamt,

			'sgstamt' => $sgstamt,

			'igstamt' => $igstamt,

			'Userid' => $_SESSION['username']

		);

		$this->db->insert(db_prefix() . 'purchasereturn', $data_array);



		if ($this->db->affected_rows() > 0) {

			$orde_no = 1;

			$this->increment_next_purchasertn_number();

			$yrdata = strtotime($data['purch_rtn_date']);

			//$date_narration = date('D-M-Y', $yrdata);



			$TransID = "";

			$i = 1;

			foreach ($es_detail as $value) {

				$item_c = $this->db->get_where(db_prefix() . 'items', array('id' => $value['ItemID'], 'PlantID' => $PlantID))->row();



				$gst_devide = 0;

				$gst_igst = 0;

				if ($data['state_f'] == 'UP') {

					$CGST = $value['CGST'];

					$SGST = $value['SGST'];

					$IGST = $value['IGST'];

					$gst = $CGST + $SGST;

					$CGST_amt = ($value['Amount'] * $CGST) / 100;

					$SGST_amt = ($value['Amount'] * $SGST) / 100;

					$IGST_amt = 0;

				} else {

					$CGST = $value['CGST'];

					$SGST = $value['SGST'];

					$IGST = $value['IGST'];

					$gst = $IGST;

					$CGST_amt = 0;

					$SGST_amt = 0;

					$IGST_amt = ($value['Amount'] * $IGST) / 100;

				}



				$TransID = $value['purchaseId'];





				$data_array_result = array(

					'PlantID' => $PlantID,

					'FY' => $FY,

					'cnfid' => 1,

					'OrderID' => $new_purchaseRtn_orderNumbar,

					'TransDate' => $Transdate,

					'BillID' => $value['purchaseId'],

					'TransDate2' => $Transdate,

					'GodownID' => $GodownID,

					'TType' => 'N',

					'TType2' => 'PurchaseReturn',

					'AccountID' => $acc_id->AccountID,

					'ItemID' => $value['ItemID'],

					'CaseQty' => $value['pack'],

					'PurchRate' => $value['Purchrate'],

					'SaleRate' => $value['Purchrate'],

					'BasicRate' => $value['Purchrate'],

					'SuppliedIn' => 1,

					'Cases' => $value['RtnCases'],

					'OrderQty' => $value['inuint'],

					'BilledQty' => $value['inuint'],

					'DiscAmt' => $value['discount_money'],

					'DiscPerc' => $value['disc'],

					'gst' => $gst,

					'cgst' => $CGST,

					'sgst' => $SGST,

					'igst' => $IGST,

					'cgstamt' => $CGST_amt,

					'sgstamt' => $SGST_amt,

					'igstamt' => $IGST_amt,

					'OrderAmt' => $value['Amount'],

					'ChallanAmt' => $value['Amount'],

					'Ordinalno' => $i,

					'UserID' => $_SESSION['username'],

				);

				$this->db->insert(db_prefix() . 'history', $data_array_result);

				$i++;

			}



			$narrations = 'By PurchRtnID ' . $new_purchaseRtn_orderNumbar . ' / ' . $data['purch_rtn_date'];

			$ledger_credit = array(

				"PlantID" => $PlantID,

				"Transdate" => $Transdate,

				"TransDate2" => date('Y-m-d H:i:s'),

				"VoucherID" => $new_purchaseRtn_orderNumbar,

				"AccountID" => $acc_id->AccountID,

				"TType" => "D",

				"Amount" => $Invamt,

				"BillNo" => $TransID,

				"Narration" => $narrations,

				"PassedFrom" => "PURCHASERTN",

				"OrdinalNo" => $orde_no,

				"UserID" => $_SESSION['username'],

				"FY" => $FY

			);

			$this->db->insert(db_prefix() . 'accountledger', $ledger_credit);

			$orde_no++;





			if ($Othamt > 0 || $Othamt < 0) {

				$ledger_otherAct = array(

					"PlantID" => $PlantID,

					"Transdate" => $Transdate,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $new_purchaseRtn_orderNumbar,

					"AccountID" => $OthAccountID,

					"TType" => "C",

					"Amount" => $Othamt,

					"BillNo" => $TransID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $orde_no,

					"UserID" => $_SESSION['username'],

					"FY" => $FY

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_otherAct);

				$orde_no++;



			}



			if ($Frtamt > 0 || $Frtamt < 0) {

				// Frt Account ledger

				$ledger_frtAct = array(

					"PlantID" => $PlantID,

					"Transdate" => $Transdate,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $new_purchaseRtn_orderNumbar,

					"AccountID" => $FrtAccountID,

					"TType" => "C",

					"Amount" => $Frtamt,

					"BillNo" => $TransID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $orde_no,

					"UserID" => $_SESSION['username'],

					"FY" => $FY

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_frtAct);

				$orde_no++;



			}



			// Credit ledger for selected account



			$ledger_debit = array(

				"PlantID" => $PlantID,

				"Transdate" => $Transdate,

				"TransDate2" => date('Y-m-d H:i:s'),

				"VoucherID" => $new_purchaseRtn_orderNumbar,

				"AccountID" => "PURCH",

				"TType" => "C",

				"Amount" => $purchase_amt,

				"BillNo" => $TransID,

				"Narration" => $narrations,

				"PassedFrom" => "PURCHASERTN",

				"OrdinalNo" => $orde_no,

				"UserID" => $_SESSION['username'],

				"FY" => $FY

			);

			$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

			$orde_no++;



			if ($igstamt != 0.00) {

				$gst = $igstamt;

				$ledger_igst = array(

					"PlantID" => $PlantID,

					"Transdate" => $Transdate,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $new_purchaseRtn_orderNumbar,

					"AccountID" => "IGST",

					"TType" => "C",

					"Amount" => $gst,

					"BillNo" => $TransID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $orde_no,

					"UserID" => $_SESSION['username'],

					"FY" => $FY

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_igst);

				$orde_no++;



			} else {

				//cgst ledger creation

				$gst1 = $cgstamt;

				$ledger_cgst = array(

					"PlantID" => $PlantID,

					"Transdate" => $Transdate,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $new_purchaseRtn_orderNumbar,

					"AccountID" => "CGST",

					"TType" => "C",

					"Amount" => $gst1,

					"BillNo" => $TransID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $orde_no,

					"UserID" => $_SESSION['username'],

					"FY" => $FY

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_cgst);

				$orde_no++;



				//sgst ledger creation             

				$gst2 = $sgstamt;



				$ledger_sgst = array(

					"PlantID" => $PlantID,

					"Transdate" => $Transdate,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $new_purchaseRtn_orderNumbar,

					"AccountID" => "SGST",

					"TType" => "C",

					"Amount" => $gst2,

					"BillNo" => $TransID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $orde_no,

					"UserID" => $_SESSION['username'],

					"FY" => $FY

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_sgst);

				$orde_no++;



			}



			if ($RoundOffAmt > 0 || $RoundOffAmt < 0) {

				$RoundOffAmt = $RoundOffAmt;

				$ledger_ROUNDOFF = array(

					"PlantID" => $PlantID,

					"Transdate" => $Transdate,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $new_purchaseRtn_orderNumbar,

					"AccountID" => "ROUNDOFF",

					"TType" => "C",

					"Amount" => $RoundOffAmt,

					"BillNo" => $TransID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $orde_no,

					"UserID" => $_SESSION['username'],

					"FY" => $FY

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_ROUNDOFF);

				$orde_no++;



			}



			return true;

		}

	}

	public function increment_next_purchasertn_number()
	{

		// Update next TAX Transaction number in settings

		$FY = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');

		if ($selected_company == 1) {

			$this->db->where('name', 'next_purchasertn_number_for_cspl');



		} elseif ($selected_company == 2) {

			$this->db->where('name', 'next_purchasertn_number_for_cff');



		} elseif ($selected_company == 3) {

			$this->db->where('name', 'next_purchasertn_number_for_cbu');



		}



		$this->db->set('value', 'value+1', false);

		$this->db->WHERE('FY', $FY);

		$this->db->update(db_prefix() . 'options');

	}

	public function load_data_for_purchaseRtn($data)
	{

		$from_date = to_sql_date($data["from_date"]);

		$to_date = to_sql_date($data["to_date"]);

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'purchasereturn.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'purchasereturn.FY = "' . $fy . '" AND ' . db_prefix() . 'purchasereturn.PlantID = "' . $selected_company . '" ORDER BY PurchRtnID ASC';



		$sql = 'SELECT ' . db_prefix() . 'purchasereturn.*,  

			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasereturn.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . $selected_company . ') as AccountName

			FROM ' . db_prefix() . 'purchasereturn WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}

	public function get_unique_purchasereturn($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $this->session->userdata('finacial_year');

		$this->db->select();

		$this->db->from(db_prefix() . 'purchasereturn');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasereturn.AccountID', 'left');

		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');

		$this->db->join(db_prefix() . 'accountbalances', db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND ' . db_prefix() . 'accountbalances.FY ="' . $year . '"', 'left');

		$this->db->where(db_prefix() . 'purchasereturn.PurchRtnID', $id);

		$this->db->where(db_prefix() . 'purchasereturn.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'purchasereturn.FY', $year);

		return $this->db->get()->row();

	}



	public function get_unique_historyreturn($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID', 'left');

		// $this->db->join(db_prefix() . 'history', db_prefix() . 'history.OrderID = ' . db_prefix() . 'purchasemaster.PurchID', 'left');

		$this->db->where(db_prefix() . 'history.OrderID', $id);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		return $this->db->get()->result_array();

	}

	public function get_pReturn_order_detail($purchRtn_id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];



		$this->db->select(db_prefix() . 'history.*,' . db_prefix() . 'items.*,(' . db_prefix() . 'history.BilledQty) AS Cases');

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID', 'left');

		$this->db->where(db_prefix() . 'history.OrderID', $purchRtn_id);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		$data = $this->db->get()->result_array();



		foreach ($data as $key => $value) {

			$this->db->select(db_prefix() . 'history.BilledQty');

			$this->db->from(db_prefix() . 'history');

			$this->db->where(db_prefix() . 'history.OrderID', $value['BillID']);

			$this->db->where(db_prefix() . 'history.ItemID', $value['ItemID']);

			$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

			$this->db->where(db_prefix() . 'history.FY', $year);

			$data_purchase = $this->db->get()->row();

			$data[$key]['Net_total'] = round($value['ChallanAmt'] + $value['cgstamt'] + $value['sgstamt'] + $value['igstamt'], 2);



			$data[$key]['PurchQty'] = $data_purchase->BilledQty;

		}

		return $data;



	}



	public function update_purchaseRtn_order($data, $id)
	{

		$selected_company = $this->session->userdata('root_company');

		$fy = $this->session->userdata('finacial_year');



		if (isset($data['pur_order_detail'])) {

			$pur_order_detail = json_decode($data['pur_order_detail']);

			unset($data['pur_order_detail']);

			$es_detail = [];

			$row = [];

			$rq_val = [];

			$header = [];

			$header[] = 'ItemID';

			$header[] = 'description';

			$header[] = 'case_qty';

			$header[] = 'BillID';

			$header[] = 'PurchQty';

			$header[] = 'BasicRate';

			$header[] = 'Cases';

			$header[] = 'BilledQty';

			$header[] = 'ChallanAmt';

			$header[] = 'DiscPerc';

			$header[] = 'DiscAmt';

			$header[] = 'cgst';

			$header[] = 'sgst';

			$header[] = 'igst';

			$header[] = 'Net_total';



			foreach ($pur_order_detail as $key => $value) {



				if ($value[0] != '') {

					$es_detail[] = array_combine($header, $value);

				}

			}

		}

		$old_purRtn_details = $this->purchase_model->get_purchaseRtn_detail($id);

		$acc_id = $this->db->select('AccountID')->get_where(db_prefix() . 'clients', array('userid' => $data['vendor']))->row();

		$back_ItCount = $this->db->select('*')->get_where(db_prefix() . 'purchasereturn', array('PurchRtnID' => $id, 'PlantID' => $selected_company))->row();



		// Add PurchaseReturnMaster Audit record 

		$PurchaseRtnAudit = array(

			"PlantID" => $back_ItCount->PlantID,

			"FY" => $back_ItCount->FY,

			"BT" => $back_ItCount->BT,

			"PurchRtnID" => $back_ItCount->PurchRtnID,

			"Transdate" => $back_ItCount->Transdate,

			"AccountID" => $back_ItCount->AccountID,

			"FrtAccountID" => $back_ItCount->FrtAccountID,

			"OthAccountID" => $back_ItCount->OthAccountID,

			"Purchamt" => $back_ItCount->Purchamt,

			"Discamt" => $back_ItCount->Discamt,

			"Frtamt" => $back_ItCount->Frtamt,

			"Othamt" => $back_ItCount->Othamt,

			"RoundOffAmt" => $back_ItCount->RoundOffAmt,

			"Invamt" => $back_ItCount->Invamt,

			"ItCount" => $back_ItCount->ItCount,

			"Userid" => $back_ItCount->Userid,

			"cgstamt" => $back_ItCount->cgstamt,

			"sgstamt" => $back_ItCount->sgstamt,

			"igstamt" => $back_ItCount->igstamt,

			"UserID2" => $this->session->userdata('username'),

			"Lupdate" => date('Y-m-d H:i:s'),

		);

		if ($this->db->insert(db_prefix() . 'purchasereturn_Audit', $PurchaseRtnAudit)) {

			foreach ($old_purRtn_details as $key => $value) {

				$Item_audit = array(

					"PlantID" => $value['PlantID'],

					"FY" => $value['FY'],

					"OrderID" => $value['OrderID'],

					"BillID" => $value['BillID'],

					"TransID" => $value['TransID'],

					"IsSchemeYN" => $value['IsSchemeYN'],

					"TransDate" => $value['TransDate'],

					"TransDate2" => $value['TransDate2'],

					"TType" => $value['TType'],

					"TType2" => $value['TType2'],

					"AccountID" => $value['AccountID'],

					"ItemID" => $value['ItemID'],

					"GodownID" => $value['GodownID'],

					"PurchRate" => $value['PurchRate'],

					"Mrp" => $value['Mrp'],

					"SaleRate" => $value['SaleRate'],

					"SuppliedIn" => $value['SuppliedIn'],

					"OrderQty" => $value['OrderQty'],

					"eOrderQty" => $value['eOrderQty'],

					"ereason" => $value['ereason'],

					"BilledQty" => $value['BilledQty'],

					"DiscPerc" => $value['DiscPerc'],

					"DiscAmt" => $value['DiscAmt'],

					"cgst" => $value['cgst'],

					"cgstamt" => $value['cgstamt'],

					"sgst" => $value['sgst'],

					"sgstamt" => $value['sgstamt'],

					"igst" => $value['igst'],

					"igstamt" => $value['igstamt'],

					"CaseQty" => $value['CaseQty'],

					"Cases" => $value['Cases'],

					"OrderAmt" => $value['OrderAmt'],

					"ChallanAmt" => $value['ChallanAmt'],

					"NetOrderAmt" => $value['NetOrderAmt'],

					"NetChallanAmt" => $value['NetChallanAmt'],

					"Ordinalno" => $value['Ordinalno'],

					"rowid" => $value['rowid'],

					"UserID" => $value['UserID'],

					"cnfid" => $value['cnfid'],

					"UserID2" => $this->session->userdata('username'),

					"Lupdate" => date('Y-m-d H:i:s'),

				);

				$this->db->insert(db_prefix() . 'history_Audit', $Item_audit);

			}

		}

		$ItCount = count($es_detail);

		$new_purchaseRtn_orderNumbar = $data['purchRtnID'];

		$PurchRtnID = $new_purchaseRtn_orderNumbar;



		$old_date = to_sql_date($data['old_purRtnDate']);

		$new_date = to_sql_date($data['purch_rtn_date']) . " " . date('H:i:m');



		$FrtAccountID = $data['Freight_1'];

		$Frtamt = $data['Freight_AMT'];

		$OthAccountID = $data['Other_ac'];

		$Othamt = $data['Other_amt'];

		$Discamt = $data['dc_total'];

		$cgstamt = $data['CGST_amt'];

		$sgstamt = $data['SGST_AMT'];

		$RoundOffAmt = $data['Round_OFF'];

		$igstamt = $data['IGST_amt'];



		$BillID = $data['purchase_id_store'];

		$Invamt = str_replace(",", "", $data['Invoice_amt']);

		$purchase_amt = str_replace(",", "", $data['total_mn']);





		$data_array = array(

			'AccountID' => $acc_id->AccountID,

			'Transdate' => $new_date,

			'FrtAccountID' => $FrtAccountID,

			'Purchamt' => $purchase_amt,

			'Discamt' => $Discamt,

			'Frtamt' => $Frtamt,

			'Othamt' => $Othamt,

			'Invamt' => $Invamt,

			'ItCount' => $ItCount,

			'RoundOffAmt' => $RoundOffAmt,

			'OthAccountID' => $OthAccountID,

			'cgstamt' => $cgstamt,

			'sgstamt' => $sgstamt,

			'igstamt' => $igstamt,

			'UserID2' => $this->session->userdata('username'),

			'Lupdate' => date('Y-m-d H:i:s'),

		);



		$this->db->where('PlantID', $selected_company);

		$this->db->LIKE('FY', $fy);

		$this->db->where('PurchRtnID', $id);

		$this->db->update(db_prefix() . 'purchasereturn', $data_array);

		if ($this->db->affected_rows() > 0) {





			$ord_no = 1;

			$narrations = 'By PurchRtnID ' . $new_purchaseRtn_orderNumbar . ' / ' . $data['purch_rtn_date'];



			// Debit ledger && update  Balance



			$get_pre_debit_amt = $this->get_pre_ledger_amt($data['vendor_code'], $new_purchaseRtn_orderNumbar);





			// delete previous ledger entry

			if ($get_pre_debit_amt) {

				$ledger_audit = array(

					"PlantID" => $get_pre_debit_amt->PlantID,

					"FY" => $get_pre_debit_amt->FY,

					"Transdate" => $get_pre_debit_amt->Transdate,

					"TransDate2" => $get_pre_debit_amt->TransDate2,

					"VoucherID" => $get_pre_debit_amt->VoucherID,

					"AccountID" => $get_pre_debit_amt->AccountID,

					"TType" => $get_pre_debit_amt->TType,

					"Amount" => $get_pre_debit_amt->Amount,

					"Narration" => $get_pre_debit_amt->Narration,

					"PassedFrom" => $get_pre_debit_amt->PassedFrom,

					"OrdinalNo" => $get_pre_debit_amt->OrdinalNo,

					"UserID" => $get_pre_debit_amt->UserID,

					"Lupdate" => date('Y-m-d H:i:s'),

					"UserID2" => $this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);

				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $fy);

				$this->db->LIKE('AccountID', $data['vendor_code']);

				$this->db->LIKE('VoucherID', $PurchRtnID);

				$this->db->delete(db_prefix() . 'accountledger');

			}





			// create new ledger entry

			$ledger_credit = array(

				"PlantID" => $selected_company,

				"Transdate" => $new_date,

				"TransDate2" => date('Y-m-d H:i:s'),

				"VoucherID" => $PurchRtnID,

				"AccountID" => $acc_id->AccountID,

				"TType" => "D",

				"Amount" => $Invamt,

				"BillNo" => $BillID,

				"Narration" => $narrations,

				"PassedFrom" => "PURCHASERTN",

				"OrdinalNo" => $ord_no,

				"UserID" => $_SESSION['username'],

				"FY" => $fy

			);

			$this->db->insert(db_prefix() . 'accountledger', $ledger_credit);

			$ord_no++;

			// Credit ledger && update  Balance

			$act = 'PURCH';

			$get_pre_credit_amt1 = $this->get_pre_ledger_amt($act, $PurchRtnID);

			// delete previous ledger entry



			if ($get_pre_credit_amt1) {

				$ledger_audit = array(

					"PlantID" => $get_pre_credit_amt1->PlantID,

					"FY" => $get_pre_credit_amt1->FY,

					"Transdate" => $get_pre_credit_amt1->Transdate,

					"TransDate2" => $get_pre_credit_amt1->TransDate2,

					"VoucherID" => $get_pre_credit_amt1->VoucherID,

					"AccountID" => $get_pre_credit_amt1->AccountID,

					"TType" => $get_pre_credit_amt1->TType,

					"Amount" => $get_pre_credit_amt1->Amount,

					"Narration" => $get_pre_credit_amt1->Narration,

					"PassedFrom" => $get_pre_credit_amt1->PassedFrom,

					"OrdinalNo" => $get_pre_credit_amt1->OrdinalNo,

					"UserID" => $get_pre_credit_amt1->UserID,

					"Lupdate" => date('Y-m-d H:i:s'),

					"UserID2" => $this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $fy);

				$this->db->where('AccountID', "PURCH");

				$this->db->where('VoucherID', $PurchRtnID);

				$this->db->delete(db_prefix() . 'accountledger');

			}

			// create new ledger entry

			$ledger_credit = array(

				"PlantID" => $selected_company,

				"Transdate" => $new_date,

				"TransDate2" => date('Y-m-d H:i:s'),

				"VoucherID" => $PurchRtnID,

				"AccountID" => "PURCH",

				"TType" => "C",

				"Amount" => $purchase_amt,

				"BillNo" => $BillID,

				"Narration" => $narrations,

				"PassedFrom" => "PURCHASERTN",

				"OrdinalNo" => $ord_no,

				"UserID" => $_SESSION['username'],

				"FY" => $fy

			);

			$this->db->insert(db_prefix() . 'accountledger', $ledger_credit);

			$ord_no++;

			// Other Account ledger

			if ($selected_company == "3") {

				$othrAct = 'ME';

			} else {

				$othrAct = '92';

			}

			$get_pre_credit_amt_o = $this->get_pre_ledger_amt($othrAct, $PurchRtnID);

			if ($get_pre_credit_amt_o) {



				$ledger_audit = array(

					"PlantID" => $get_pre_credit_amt_o->PlantID,

					"FY" => $get_pre_credit_amt_o->FY,

					"Transdate" => $get_pre_credit_amt_o->Transdate,

					"TransDate2" => $get_pre_credit_amt_o->TransDate2,

					"VoucherID" => $get_pre_credit_amt_o->VoucherID,

					"AccountID" => $get_pre_credit_amt_o->AccountID,

					"TType" => $get_pre_credit_amt_o->TType,

					"Amount" => $get_pre_credit_amt_o->Amount,

					"Narration" => $get_pre_credit_amt_o->Narration,

					"PassedFrom" => $get_pre_credit_amt_o->PassedFrom,

					"OrdinalNo" => $get_pre_credit_amt_o->OrdinalNo,

					"UserID" => $get_pre_credit_amt_o->UserID,

					"Lupdate" => date('Y-m-d H:i:s'),

					"UserID2" => $this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $fy);

				$this->db->where('AccountID', $othrAct);

				$this->db->where('VoucherID', $PurchRtnID);

				$this->db->delete(db_prefix() . 'accountledger');



			}

			if ($Othamt > 0 || $Othamt < 0) {



				$ledger_otherAct = array(

					"PlantID" => $selected_company,

					"Transdate" => $new_date,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $PurchRtnID,

					"AccountID" => $othrAct,

					"TType" => "C",

					"Amount" => $Othamt,

					"BillNo" => $BillID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $ord_no,

					"UserID" => $_SESSION['username'],

					"FY" => $fy

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_otherAct);

				$ord_no++;

			}



			// Frt Account ledger

			$frtAct = '209';

			$get_pre_credit_amt_f = $this->get_pre_ledger_amt($frtAct, $PurchRtnID);

			if ($get_pre_credit_amt_f) {



				$ledger_audit = array(

					"PlantID" => $get_pre_credit_amt_f->PlantID,

					"FY" => $get_pre_credit_amt_f->FY,

					"Transdate" => $get_pre_credit_amt_f->Transdate,

					"TransDate2" => $get_pre_credit_amt_f->TransDate2,

					"VoucherID" => $get_pre_credit_amt_f->VoucherID,

					"AccountID" => $get_pre_credit_amt_f->AccountID,

					"TType" => $get_pre_credit_amt_f->TType,

					"Amount" => $get_pre_credit_amt_f->Amount,

					"Narration" => $get_pre_credit_amt_f->Narration,

					"PassedFrom" => $get_pre_credit_amt_f->PassedFrom,

					"OrdinalNo" => $get_pre_credit_amt_f->OrdinalNo,

					"UserID" => $get_pre_credit_amt_f->UserID,

					"Lupdate" => date('Y-m-d H:i:s'),

					"UserID2" => $this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $fy);

				$this->db->where('AccountID', $frtAct);

				$this->db->where('VoucherID', $PurchRtnID);

				$this->db->delete(db_prefix() . 'accountledger');

			}

			if ($Frtamt > 0 || $Frtamt < 0) {



				$ledger_otherAct = array(

					"PlantID" => $selected_company,

					"Transdate" => $new_date,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $PurchRtnID,

					"AccountID" => $frtAct,

					"TType" => "C",

					"Amount" => $Frtamt,

					"BillNo" => $BillID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $ord_no,

					"UserID" => $_SESSION['username'],

					"FY" => $fy

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_otherAct);

				$ord_no++;

			}



			// gst ledger update && Balance

			if ($igstamt != "0.00") {

				// for igst ladger    

				$get_pre_credit_amt11 = $this->get_pre_ledger_amt('IGST', $PurchRtnID);

				$get_pre_credit_amt22 = $this->get_pre_ledger_amt('CGST', $PurchRtnID);

				$get_pre_credit_amt33 = $this->get_pre_ledger_amt('SGST', $PurchRtnID);



				// delete previous ledger entry

				//IGST

				if ($get_pre_credit_amt11) {

					$ledger_audit = array(

						"PlantID" => $get_pre_credit_amt11->PlantID,

						"FY" => $get_pre_credit_amt11->FY,

						"Transdate" => $get_pre_credit_amt11->Transdate,

						"TransDate2" => $get_pre_credit_amt11->TransDate2,

						"VoucherID" => $get_pre_credit_amt11->VoucherID,

						"AccountID" => $get_pre_credit_amt11->AccountID,

						"TType" => $get_pre_credit_amt11->TType,

						"Amount" => $get_pre_credit_amt11->Amount,

						"Narration" => $get_pre_credit_amt11->Narration,

						"PassedFrom" => $get_pre_credit_amt11->PassedFrom,

						"OrdinalNo" => $get_pre_credit_amt11->OrdinalNo,

						"UserID" => $get_pre_credit_amt11->UserID,

						"Lupdate" => date('Y-m-d H:i:s'),

						"UserID2" => $this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



					$this->db->where('PlantID', $selected_company);

					$this->db->LIKE('FY', $fy);

					$this->db->where('AccountID', "IGST");

					$this->db->where('VoucherID', $PurchRtnID);

					$this->db->delete(db_prefix() . 'accountledger');

				}



				//CGST

				if ($get_pre_credit_amt22) {

					$ledger_audit = array(

						"PlantID" => $get_pre_credit_amt22->PlantID,

						"FY" => $get_pre_credit_amt22->FY,

						"Transdate" => $get_pre_credit_amt22->Transdate,

						"TransDate2" => $get_pre_credit_amt22->TransDate2,

						"VoucherID" => $get_pre_credit_amt22->VoucherID,

						"AccountID" => $get_pre_credit_amt22->AccountID,

						"TType" => $get_pre_credit_amt22->TType,

						"Amount" => $get_pre_credit_amt22->Amount,

						"Narration" => $get_pre_credit_amt22->Narration,

						"PassedFrom" => $get_pre_credit_amt22->PassedFrom,

						"OrdinalNo" => $get_pre_credit_amt22->OrdinalNo,

						"UserID" => $get_pre_credit_amt22->UserID,

						"Lupdate" => date('Y-m-d H:i:s'),

						"UserID2" => $this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



					$this->db->where('PlantID', $selected_company);

					$this->db->LIKE('FY', $fy);

					$this->db->where('AccountID', "CGST");

					$this->db->where('VoucherID', $PurchRtnID);

					$this->db->delete(db_prefix() . 'accountledger');

				}



				//SGST



				if ($get_pre_credit_amt33) {

					$ledger_audit = array(

						"PlantID" => $get_pre_credit_amt33->PlantID,

						"FY" => $get_pre_credit_amt33->FY,

						"Transdate" => $get_pre_credit_amt33->Transdate,

						"TransDate2" => $get_pre_credit_amt33->TransDate2,

						"VoucherID" => $get_pre_credit_amt33->VoucherID,

						"AccountID" => $get_pre_credit_amt33->AccountID,

						"TType" => $get_pre_credit_amt33->TType,

						"Amount" => $get_pre_credit_amt33->Amount,

						"Narration" => $get_pre_credit_amt33->Narration,

						"PassedFrom" => $get_pre_credit_amt33->PassedFrom,

						"OrdinalNo" => $get_pre_credit_amt33->OrdinalNo,

						"UserID" => $get_pre_credit_amt33->UserID,

						"Lupdate" => date('Y-m-d H:i:s'),

						"UserID2" => $this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



					$this->db->where('PlantID', $selected_company);

					$this->db->LIKE('FY', $fy);

					$this->db->where('AccountID', "SGST");

					$this->db->where('VoucherID', $PurchRtnID);

					$this->db->delete(db_prefix() . 'accountledger');

				}





				// create new ledger entry

				$ledger_IgstAct = array(

					"PlantID" => $selected_company,

					"Transdate" => $new_date,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $PurchRtnID,

					"AccountID" => "IGST",

					"TType" => "C",

					"Amount" => $igstamt,

					"BillNo" => $BillID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $ord_no,

					"UserID" => $_SESSION['username'],

					"FY" => $fy

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_IgstAct);

				$ord_no++;

			} else {

				// for igst ladger    

				$get_pre_credit_amt11 = $this->get_pre_ledger_amt('IGST', $PurchRtnID);

				$get_pre_credit_amt22 = $this->get_pre_ledger_amt('CGST', $PurchRtnID);

				$get_pre_credit_amt33 = $this->get_pre_ledger_amt('SGST', $PurchRtnID);



				// delete previous ledger entry



				//IGST

				if ($get_pre_credit_amt11) {

					$ledger_audit = array(

						"PlantID" => $get_pre_credit_amt11->PlantID,

						"FY" => $get_pre_credit_amt11->FY,

						"Transdate" => $get_pre_credit_amt11->Transdate,

						"TransDate2" => $get_pre_credit_amt11->TransDate2,

						"VoucherID" => $get_pre_credit_amt11->VoucherID,

						"AccountID" => $get_pre_credit_amt11->AccountID,

						"TType" => $get_pre_credit_amt11->TType,

						"Amount" => $get_pre_credit_amt11->Amount,

						"Narration" => $get_pre_credit_amt11->Narration,

						"PassedFrom" => $get_pre_credit_amt11->PassedFrom,

						"OrdinalNo" => $get_pre_credit_amt11->OrdinalNo,

						"UserID" => $get_pre_credit_amt11->UserID,

						"Lupdate" => date('Y-m-d H:i:s'),

						"UserID2" => $this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



					$this->db->where('PlantID', $selected_company);

					$this->db->LIKE('FY', $fy);

					$this->db->where('AccountID', "IGST");

					$this->db->where('VoucherID', $PurchRtnID);

					$this->db->delete(db_prefix() . 'accountledger');

				}



				//CGST

				if ($get_pre_credit_amt22) {

					$ledger_audit = array(

						"PlantID" => $get_pre_credit_amt22->PlantID,

						"FY" => $get_pre_credit_amt22->FY,

						"Transdate" => $get_pre_credit_amt22->Transdate,

						"TransDate2" => $get_pre_credit_amt22->TransDate2,

						"VoucherID" => $get_pre_credit_amt22->VoucherID,

						"AccountID" => $get_pre_credit_amt22->AccountID,

						"TType" => $get_pre_credit_amt22->TType,

						"Amount" => $get_pre_credit_amt22->Amount,

						"Narration" => $get_pre_credit_amt22->Narration,

						"PassedFrom" => $get_pre_credit_amt22->PassedFrom,

						"OrdinalNo" => $get_pre_credit_amt22->OrdinalNo,

						"UserID" => $get_pre_credit_amt22->UserID,

						"Lupdate" => date('Y-m-d H:i:s'),

						"UserID2" => $this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



					$this->db->where('PlantID', $selected_company);

					$this->db->LIKE('FY', $fy);

					$this->db->where('AccountID', "CGST");

					$this->db->where('VoucherID', $PurchRtnID);

					$this->db->delete(db_prefix() . 'accountledger');

				}



				//SGST

				if ($get_pre_credit_amt33) {

					$ledger_audit = array(

						"PlantID" => $get_pre_credit_amt33->PlantID,

						"FY" => $get_pre_credit_amt33->FY,

						"Transdate" => $get_pre_credit_amt33->Transdate,

						"TransDate2" => $get_pre_credit_amt33->TransDate2,

						"VoucherID" => $get_pre_credit_amt33->VoucherID,

						"AccountID" => $get_pre_credit_amt33->AccountID,

						"TType" => $get_pre_credit_amt33->TType,

						"Amount" => $get_pre_credit_amt33->Amount,

						"Narration" => $get_pre_credit_amt33->Narration,

						"PassedFrom" => $get_pre_credit_amt33->PassedFrom,

						"OrdinalNo" => $get_pre_credit_amt33->OrdinalNo,

						"UserID" => $get_pre_credit_amt33->UserID,

						"Lupdate" => date('Y-m-d H:i:s'),

						"UserID2" => $this->session->userdata('username')

					);

					$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



					$this->db->where('PlantID', $selected_company);

					$this->db->LIKE('FY', $fy);

					$this->db->where('AccountID', "SGST");

					$this->db->where('VoucherID', $PurchRtnID);

					$this->db->delete(db_prefix() . 'accountledger');

				}



				// create new ledger entry

				// CGST

				$ledger_debit = array(

					"PlantID" => $selected_company,

					"Transdate" => $new_date,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $PurchRtnID,

					"AccountID" => "CGST",

					"TType" => "C",

					"Amount" => $sgstamt,

					"BillNo" => $BillID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $ord_no,

					"UserID" => $_SESSION['username'],

					"FY" => $fy

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

				$ord_no++;

				// create new ledger entry

				// SGST

				$ledger_debit = array(

					"PlantID" => $selected_company,

					"Transdate" => $new_date,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $PurchRtnID,

					"AccountID" => "SGST",

					"TType" => "C",

					"Amount" => $sgstamt,

					"BillNo" => $BillID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $ord_no,

					"UserID" => $_SESSION['username'],

					"FY" => $fy

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

				$ord_no++;

			}

			//for RoundOffAmt ladger  

			$get_pre_credit_amt33 = $this->get_pre_ledger_amt('ROUNDOFF', $PurchRtnID);

			if ($get_pre_credit_amt33) {



				$ledger_audit = array(

					"PlantID" => $get_pre_credit_amt33->PlantID,

					"FY" => $get_pre_credit_amt33->FY,

					"Transdate" => $get_pre_credit_amt33->Transdate,

					"TransDate2" => $get_pre_credit_amt33->TransDate2,

					"VoucherID" => $get_pre_credit_amt33->VoucherID,

					"AccountID" => $get_pre_credit_amt33->AccountID,

					"TType" => $get_pre_credit_amt33->TType,

					"Amount" => $get_pre_credit_amt33->Amount,

					"Narration" => $get_pre_credit_amt33->Narration,

					"PassedFrom" => $get_pre_credit_amt33->PassedFrom,

					"OrdinalNo" => $get_pre_credit_amt33->OrdinalNo,

					"UserID" => $get_pre_credit_amt33->UserID,

					"Lupdate" => date('Y-m-d H:i:s'),

					"UserID2" => $this->session->userdata('username')

				);

				$this->db->insert(db_prefix() . 'accountledgeraudit', $ledger_audit);



				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $fy);

				$this->db->where('AccountID', "ROUNDOFF");

				$this->db->where('VoucherID', $PurchRtnID);

				$this->db->delete(db_prefix() . 'accountledger');

			}



			if ($RoundOffAmt > 0 || $RoundOffAmt < 0) {



				$ledger_debit = array(

					"PlantID" => $selected_company,

					"Transdate" => $new_date,

					"TransDate2" => date('Y-m-d H:i:s'),

					"VoucherID" => $PurchRtnID,

					"AccountID" => "ROUNDOFF",

					"TType" => "C",

					"Amount" => $RoundOffAmt,

					"BillNo" => $BillID,

					"Narration" => $narrations,

					"PassedFrom" => "PURCHASERTN",

					"OrdinalNo" => $ord_no,

					"UserID" => $_SESSION['username'],

					"FY" => $fy

				);

				$this->db->insert(db_prefix() . 'accountledger', $ledger_debit);

			}



			$deleted_item = array();

			$new_items = array();

			foreach ($es_detail as $value) {

				array_push($new_items, $value['ItemID']);

			}

			$old_ItemID = array();

			foreach ($old_purRtn_details as $key => $value) {



				array_push($old_ItemID, $value["ItemID"]);

				//check deleted item

				if (!in_array($value["ItemID"], $new_items)) {

					array_push($deleted_item, $value["ItemID"]);

				}

			}

			if ($acc_id->AccountID == $data["vendor_code"]) {

				$i = 1;

				foreach ($es_detail as $value) {



					$gst_devide = 0;

					$gst_igst = 0;

					if ($data['state_f'] == 'UP') {

						$CGST = $value['cgst'];

						$SGST = $value['sgst'];

						$IGST = $value['igst'];

						$gst = $CGST + $SGST;

						$CGST_amt = ($value['ChallanAmt'] * $CGST) / 100;

						$SGST_amt = ($value['ChallanAmt'] * $SGST) / 100;

						$IGST_amt = 0;

					} else {

						$CGST = $value['cgst'];

						$SGST = $value['sgst'];

						$IGST = $value['igst'];

						$gst = $IGST;

						$CGST_amt = 0;

						$SGST_amt = 0;

						$IGST_amt = ($value['ChallanAmt'] * $IGST) / 100;

					}

					if (in_array($value['ItemID'], $old_ItemID)) {

						$Cases = $value['Cases'] / $value['case_qty'];

						$data_array_result_update = array(

							'TransDate2' => $new_date,

							'CaseQty' => $value['case_qty'],

							'PurchRate' => $value['BasicRate'],

							'SaleRate' => $value['BasicRate'],

							'BasicRate' => $value['BasicRate'],

							'SuppliedIn' => 1,

							'Cases' => $Cases,

							'OrderQty' => $value['BilledQty'],

							'BilledQty' => $value['BilledQty'],

							'DiscAmt' => $value['DiscAmt'],

							'DiscPerc' => $value['DiscPerc'],

							'gst' => $gst,

							'cgst' => $CGST,

							'sgst' => $SGST,

							'igst' => $IGST,

							'cgstamt' => $CGST_amt,

							'sgstamt' => $SGST_amt,

							'igstamt' => $IGST_amt,

							'OrderAmt' => $value['ChallanAmt'],

							'ChallanAmt' => $value['ChallanAmt'],

							'NetOrderAmt' => $value['Net_total'],

							'NetChallanAmt' => $value['Net_total'],

							'UserID2' => $_SESSION['username'],

							'Lupdate' => $new_date,

						);

						$this->db->where('OrderID', $new_purchaseRtn_orderNumbar);

						$this->db->where('ItemID', $value['ItemID']);

						$this->db->where('PlantID', $selected_company);

						$this->db->LIKE('FY', $fy);

						$this->db->update(db_prefix() . 'history', $data_array_result_update);



					} else {

						$Cases = $value['Cases'] / $value['case_qty'];

						$data_array_result_add = array(

							'PlantID' => $selected_company,

							'FY' => $fy,

							'cnfid' => 1,

							'OrderID' => $new_purchaseRtn_orderNumbar,

							'TransDate' => $new_date,

							'BillID' => $BillID,

							'TransDate2' => $new_date,

							'TType' => 'N',

							'TType2' => 'PurchaseReturn',

							'AccountID' => $acc_id->AccountID,

							'ItemID' => $value['ItemID'],

							'CaseQty' => $value['case_qty'],

							'PurchRate' => $value['BasicRate'],

							'SaleRate' => $value['BasicRate'],

							'BasicRate' => $value['BasicRate'],

							'SuppliedIn' => 1,

							'Cases' => $Cases,

							'OrderQty' => $value['BilledQty'],

							'BilledQty' => $value['BilledQty'],

							'DiscPerc' => $value['DiscPerc'],

							'DiscAmt' => $value['DiscAmt'],

							'gst' => $gst,

							'cgst' => $CGST,

							'sgst' => $SGST,

							'igst' => $IGST,

							'cgstamt' => $CGST_amt,

							'sgstamt' => $SGST_amt,

							'igstamt' => $IGST_amt,

							'OrderAmt' => $value['ChallanAmt'],

							'ChallanAmt' => $value['ChallanAmt'],

							'NetOrderAmt' => $value['Net_total'],

							'NetChallanAmt' => $value['Net_total'],

							'Ordinalno' => $i,

							'UserID' => $_SESSION['username'],

						);

						$this->db->insert(db_prefix() . 'history', $data_array_result_add);

					}

				}



				foreach ($deleted_item as $values) {

					$this->db->where('PlantID', $selected_company);

					$this->db->LIKE('FY', $fy);

					$this->db->where('AccountID', $data['vendor_code']);

					$this->db->where('OrderID', $new_purchaseRtn_orderNumbar);

					$this->db->where('ItemID', $values);

					$this->db->delete(db_prefix() . 'history');

				}

			} else {

				$this->db->where('PlantID', $selected_company);

				$this->db->LIKE('FY', $fy);

				$this->db->where('AccountID', $data['vendor_code']);

				$this->db->where('OrderID', $new_purchaseRtn_orderNumbar);

				$this->db->delete(db_prefix() . 'history');

				$i = 1;

				foreach ($es_detail as $value) {



					$gst_devide = 0;

					$gst_igst = 0;

					if ($data['state_f'] == 'UP') {

						$CGST = $value['cgst'];

						$SGST = $value['sgst'];

						$IGST = $value['igst'];

						$gst = $CGST + $SGST;

						$CGST_amt = ($value['ChallanAmt'] * $CGST) / 100;

						$SGST_amt = ($value['ChallanAmt'] * $SGST) / 100;

						$IGST_amt = 0;

					} else {

						$CGST = $value['cgst'];

						$SGST = $value['sgst'];

						$IGST = $value['igst'];

						$gst = $IGST;

						$CGST_amt = 0;

						$SGST_amt = 0;

						$IGST_amt = ($value['ChallanAmt'] * $IGST) / 100;

					}

					$Cases = $value['Cases'] / $value['case_qty'];

					$data_array_result_add = array(

						'PlantID' => $selected_company,

						'FY' => $fy,

						'cnfid' => 1,

						'OrderID' => $new_purchaseRtn_orderNumbar,

						'TransDate' => $new_date,

						'BillID' => $BillID,

						'TransDate2' => $new_date,

						'TType' => 'N',

						'TType2' => 'PurchaseReturn',

						'AccountID' => $acc_id->AccountID,

						'ItemID' => $value['ItemID'],

						'CaseQty' => $value['case_qty'],

						'PurchRate' => $value['BasicRate'],

						'SaleRate' => $value['BasicRate'],

						'BasicRate' => $value['BasicRate'],

						'SuppliedIn' => 1,

						'Cases' => $Cases,

						'OrderQty' => $value['BilledQty'],

						'BilledQty' => $value['BilledQty'],

						'DiscPerc' => $value['DiscPerc'],

						'DiscAmt' => $value['DiscAmt'],

						'gst' => $gst,

						'cgst' => $CGST,

						'sgst' => $SGST,

						'igst' => $IGST,

						'cgstamt' => $CGST_amt,

						'sgstamt' => $SGST_amt,

						'igstamt' => $IGST_amt,

						'OrderAmt' => $value['ChallanAmt'],

						'ChallanAmt' => $value['ChallanAmt'],

						'NetOrderAmt' => $value['Net_total'],

						'NetChallanAmt' => $value['Net_total'],

						'Ordinalno' => $i,

						'UserID' => $_SESSION['username'],

					);

					$this->db->insert(db_prefix() . 'history', $data_array_result_add);

				}

			}

			return true;

		}

		return false;

	}

	public function get_purchaseRtn_detail($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $this->session->userdata('finacial_year');

		$this->db->select();

		$this->db->from(db_prefix() . 'history');

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		$this->db->where(db_prefix() . 'history.OrderID', $id);

		return $this->db->get()->result_array();

		// echo $this->db->last_query();die;

	}



	public function add_unit($data)
	{

		$UserID = $this->session->userdata('username');



		$insert_data = array(

			'unit_name' => $data['name'],

			'measured_in' => $data['Measured_in'],

			'UserID' => $UserID,

			'TransDate' => date('Y-m-d H:i:s'),

		);

		$this->db->insert(db_prefix() . 'qc_unit', $insert_data);

		return true;

	}



	public function get_data_table_unit()
	{

		$selected_company = $this->session->userdata('root_company');



		$data = $this->db->get(db_prefix() . 'qc_unit')->result_array();

		return $data;

	}





	public function get_data_table_parameter()
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('tblqc_parameter.*,tblqc_unit.unit_name');

		$this->db->join('tblqc_unit', 'tblqc_unit.id = tblqc_parameter.unit_id', 'LEFT');

		$Data = $this->db->get('tblqc_parameter')->result_array();

		return $Data;

	}



	public function get_unit_by_id($id = '')
	{



		$this->db->select('*');

		$selected_company = $this->session->userdata('root_company');

		$this->db->from(db_prefix() . 'qc_unit');

		if (is_numeric($id)) {

			$this->db->where(db_prefix() . 'qc_unit.id', $id);



			return $this->db->get()->row();

		}

		return $this->db->get()->result_array();

	}





	public function get_parameter_by_id($id = '')
	{



		$this->db->select('*');

		$selected_company = $this->session->userdata('root_company');

		$this->db->from(db_prefix() . 'qc_parameter');

		if (is_numeric($id)) {

			$this->db->where(db_prefix() . 'qc_parameter.id', $id);



			return $this->db->get()->row();

		}

		return $this->db->get()->result_array();

	}



	public function edit_unit($data)
	{

		$itemid = $data['itemid'];

		unset($data['itemid']);



		$update_data = array(

			'unit_name' => $data['name'],

			'measured_in' => $data['Measured_in'],

			'UserID2' => $UserID,

			'Lupdate' => date('Y-m-d H:i:s'),

		);

		$selected_company = $this->session->userdata('root_company');

		$this->db->where('id', $itemid);

		$this->db->update(db_prefix() . 'qc_unit', $update_data);



		return true;

	}



	public function edit_parameter($data)
	{

		$id = $data['itemid'];

		unset($data['itemid']);



		$update_data = array(

			'parameter_name' => $data['name'],

			'unit_id' => $data['unit'],

			'UserID2' => $UserID,

			'Lupdate' => date('Y-m-d H:i:s'),

		);

		$selected_company = $this->session->userdata('root_company');

		$this->db->where('id', $id);

		$this->db->update(db_prefix() . 'qc_parameter', $update_data);



		return true;

	}





	public function add_parameter($data)
	{

		$UserID = $this->session->userdata('username');



		$insert_data = array(

			'parameter_name' => $data['name'],

			'unit_id' => $data['unit'],

			'UserID' => $UserID,

			'TransDate' => date('Y-m-d H:i:s'),

		);

		$this->db->insert(db_prefix() . 'qc_parameter', $insert_data);

		return true;

	}





	public function get_data_items($main_group = '')
	{



		$this->db->select('*');

		$selected_company = $this->session->userdata('root_company');

		$this->db->from(db_prefix() . 'items');

		$this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'left');

		$this->db->join(db_prefix() . 'items_main_groups', '' . db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'items_sub_groups.main_DivisionID', 'left');

		$this->db->where('PlantID', $selected_company);

		if ($main_group) {

			$this->db->where(db_prefix() . 'items_main_groups.id', $main_group);

		}

		return $this->db->get()->result_array();

	}





	public function GetItemListbyGroups($main_group, $SubGroup1, $SubGroup2)
	{



		$this->db->select('*');

		$selected_company = $this->session->userdata('root_company');

		$this->db->from(db_prefix() . 'items');

		$this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'left');

		$this->db->join(db_prefix() . 'items_main_groups', '' . db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'items_sub_groups.main_DivisionID', 'left');

		$this->db->join(db_prefix() . 'qc_master', '' . db_prefix() . 'qc_master.ItemID = ' . db_prefix() . 'items.ItemID', 'left');

		$this->db->where(db_prefix() . 'qc_master.ItemID IS NULL');

		$this->db->where('PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items_main_groups.id', $main_group);

		$this->db->where(db_prefix() . 'items.SubGrpID1', $SubGroup1);

		$this->db->where(db_prefix() . 'items.SubGrpID2', $SubGroup2);

		return $this->db->get()->result_array();

	}

	public function GetItemListbyGroupsEdit($main_group, $SubGroup1, $SubGroup2, $itemId)
	{



		$this->db->select('*');

		$selected_company = $this->session->userdata('root_company');

		$this->db->from(db_prefix() . 'items');

		$this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'left');

		$this->db->join(db_prefix() . 'items_main_groups', '' . db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'items_sub_groups.main_DivisionID', 'left');

		$this->db->join(db_prefix() . 'qc_master', '' . db_prefix() . 'qc_master.ItemID = ' . db_prefix() . 'items.ItemID', 'left');

		$this->db->where(db_prefix() . 'qc_master.ItemID IS NULL');

		$this->db->where('PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items_main_groups.id', $main_group);

		$this->db->where(db_prefix() . 'items.SubGrpID1', $SubGroup1);

		$this->db->where(db_prefix() . 'items.SubGrpID2', $SubGroup2);

		// Get the result of the first query

		$items_without_qc_master = $this->db->get()->result_array();



		// Fetch the specific item by code using get_data_items_by_code function

		$additional_item = $this->get_data_items_by_code($itemId);



		// Combine both results

		$result = array_merge($additional_item, $items_without_qc_master);



		// Return the combined array

		return $result;

	}



	public function get_data_items_by_code($code)
	{



		$this->db->select('*');

		$selected_company = $this->session->userdata('root_company');

		$this->db->from(db_prefix() . 'items');

		$this->db->where('PlantID', $selected_company);

		$this->db->where('ItemID', $code);

		return $this->db->get()->result_array();

	}







	public function get_data_table_qc_master()
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('tblqc_master.ItemID,tblitems.description,tblitems.unit,tblitems_main_groups.name as group_name,tblitemsSubGroup2.name as subgroup_name');

		$this->db->join('tblitems', 'tblitems.ItemID = tblqc_master.ItemID', 'INNER');

		$this->db->join(db_prefix() . 'items_main_groups', '' . db_prefix() . 'items_main_groups.id = ' . db_prefix() . 'items.DivisionID', 'left');

		$this->db->join(db_prefix() . 'items_sub_groups', '' . db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1', 'left');

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);



		$this->db->order_by('tblqc_master.ItemID', 'asc');

		$this->db->group_by('tblqc_master.ItemID');

		$Data = $this->db->get('tblqc_master')->result_array();

		return $Data;

	}



	public function get_master_data_byId($code)
	{



		$this->db->select('*');

		$selected_company = $this->session->userdata('root_company');

		$this->db->from(db_prefix() . 'qc_master');

		$this->db->where_in('ItemID', $code);

		return $this->db->get()->result_array();

	}





	public function get_master_data_byItemId($code)
	{



		$selected_company = $this->session->userdata('root_company');

		$this->db->select('*');

		$this->db->from(db_prefix() . 'items');

		$this->db->where('ItemID', $code);

		$data = $this->db->get()->row();

		if (!empty($data)) {

			$data->SubGroup1List = $this->GetSubgroup1Data($data->MainGrpID);

			$data->SubGroup2List = $this->GetSubgroup2Data($data->SubGrpID1);

			$data->ItemList = $this->GetItemListbyGroupsEdit($data->MainGrpID, $data->SubGrpID1, $data->SubGrpID2, $code);

			$data->MasterList = $this->get_master_data_byId($code);



		}

		return $data;

	}



	public function DeleteQCMaster($code)
	{

		$this->db->where('ItemID', $code);

		if ($this->db->delete(db_prefix() . 'qc_master')) {

			return true;

		} else {

			return false;

		}

	}

	public function DeleteQCMasterParameter($id)
	{

		$this->db->where('id', $id);

		if ($this->db->delete(db_prefix() . 'qc_master')) {

			return true;

		} else {

			return false;

		}

	}

	public function DeleteQCParameter($id)
	{

		$this->db->where('id', $id);

		if ($this->db->delete(db_prefix() . 'qc_parameter')) {

			return true;

		} else {

			return false;

		}

	}

	public function DeleteQCUnit($id)
	{

		$this->db->where('id', $id);

		if ($this->db->delete(db_prefix() . 'qc_unit')) {

			return true;

		} else {

			return false;

		}

	}



	public function UpdateQCMaster($Data)
	{

		$items = $Data["item_id1"];



		$selected_company = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');

		$LogID = $this->session->userdata('username');

		$parameterAssign = $Data["parameterdataSerializedArr"];

		$ParameterAssignArray = json_decode($parameterAssign, true);

		$ParameterAssignArraylen = count($ParameterAssignArray);

		// print_r($item_id);die;

		foreach ($items as $each) {

			$item_id = $each;



			// Insert / Update 

			for ($k = 0; $k < $ParameterAssignArraylen; $k++) {

				$addtblid = $ParameterAssignArray[$k][0];

				$para_id = $ParameterAssignArray[$k][1];

				$min_range = $ParameterAssignArray[$k][2];

				$max_range = $ParameterAssignArray[$k][3];

				$validation = $ParameterAssignArray[$k][4];





				if (!empty($addtblid)) {

					$chk = $this->GetQCMasterByItemCode($item_id, $addtblid);



					if (!empty($chk)) {

						$updateArr = array(

							'para_id' => $para_id,

							'min_range' => $min_range,

							'max_range' => $max_range,

							'validation' => $validation,

							'Lupdate' => date('Y-m-d H:i:s'),

							'UserID2' => $LogID

						);



						$this->db->where('id', $addtblid);

						$this->db->update(db_prefix() . 'qc_master', $updateArr);

					} else {

						$InsArr = array(

							'ItemID' => $item_id,

							'para_id' => $para_id,

							'min_range' => $min_range,

							'max_range' => $max_range,

							'validation' => $validation,

							'TransDate' => date('Y-m-d H:i:s'),

							'UserID' => $LogID

						);

						$this->db->insert(db_prefix() . 'qc_master', $InsArr);

					}

				} else {

					$InsArr = array(

						'ItemID' => $item_id,

						'para_id' => $para_id,

						'min_range' => $min_range,

						'max_range' => $max_range,

						'validation' => $validation,

						'TransDate' => date('Y-m-d H:i:s'),

						'UserID' => $LogID

					);

					$this->db->insert(db_prefix() . 'qc_master', $InsArr);

				}

			}

		}

		return true;

	}



	public function GetItemWiseQCStatusByEntryNo($PurEntryNO)
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('*');

		$this->db->where(db_prefix() . 'ItemWiseQCStatus.PurchaseEntryNo', $PurEntryNO);

		$Data = $this->db->get('tblItemWiseQCStatus')->result_array();

		return $Data;

	}

	public function GetItemWisePendingQCStatusByEntryNo($PurEntryNO)
	{

		$selected_company = $this->session->userdata('root_company');



		$status = array('H', 'N');

		$this->db->select('*');

		$this->db->where(db_prefix() . 'ItemWiseQCStatus.PurchaseEntryNo', $PurEntryNO);

		$this->db->where_in(db_prefix() . 'ItemWiseQCStatus.Status', $status);

		$Data = $this->db->get('tblItemWiseQCStatus')->result_array();

		return $Data;

	}

	public function GetReconsileEntry($PurEntryNO)
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('*');

		$this->db->where(db_prefix() . 'ReconsileMaster.TransID', $PurEntryNO);

		$this->db->where(db_prefix() . 'ReconsileMaster.PassedFrom', 'PURCHASE');

		$Data = $this->db->get('tblReconsileMaster')->result_array();

		return $Data;

	}

	public function GetQCMasterByItemCode($ItemID, $id)
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('*');

		$this->db->where(db_prefix() . 'qc_master.ItemID', $ItemID);

		$this->db->where(db_prefix() . 'qc_master.id', $id);

		$Data = $this->db->get('tblqc_master')->row();

		return $Data;

	}

	public function ChkQCMasterByItemID($ItemID)
	{

		$selected_company = $this->session->userdata('root_company');



		$this->db->select('*');

		$this->db->where(db_prefix() . 'qc_master.ItemID', $ItemID);

		$Data = $this->db->get('tblqc_master')->row();

		return $Data;

	}



	public function GetShippingAddress($Customer_id = "", $ShippingID = "")
	{

		$this->db->select('tblclients.AccountID,tblclients.vat,tblclientwiseshippingdata.id,tblclientwiseshippingdata.ShippingState,tblclientwiseshippingdata.ShippingCity,

			tblclientwiseshippingdata.ShippingAdrees,tblclientwiseshippingdata.ShippingPin,tblxx_statelist.state_name,tblxx_citylist.city_name');

		$this->db->join(db_prefix() . 'clientwiseshippingdata', db_prefix() . 'clientwiseshippingdata.AccountID = ' . db_prefix() . 'clients.AccountID', "LEFT");

		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clientwiseshippingdata.ShippingState', "LEFT");

		$this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'xx_citylist.id = ' . db_prefix() . 'clientwiseshippingdata.ShippingCity', "LEFT");

		if ($ShippingID) {

			$this->db->where('tblclientwiseshippingdata.id', $ShippingID);

			return $this->db->get(db_prefix() . 'clients')->row();

		} else {

			$this->db->where('tblclients.AccountID', $Customer_id);

			$this->db->order_by('tblclientwiseshippingdata.IsBilling', 'DESC');

			return $this->db->get(db_prefix() . 'clients')->result_array();

		}

	}



	public function get_next_order_no_by_category($category_id)
	{
		if (!$category_id)
			return '';
		$this->db->where('ItemCategory', $category_id);
		$count = $this->db->count_all_results('tblPurchaseOrderMaster');
		$next_no = $count + 1;
		// You can format the order number as needed, e.g., with leading zeros or prefix
		return 'PO' . str_pad($next_no, 5, '0', STR_PAD_LEFT);
	}


public function update_purchase_order_PO($data, $id)
{
    $plant = $this->session->userdata('root_company');
    $user  = $this->session->userdata('staff_user_id');
    $fy    = $this->session->userdata('finacial_year');

    // Helper: safely get scalar value (if array, take first element)
    $scalar = function ($val, $default = '') {
        if (is_array($val))
            return isset($val[0]) ? $val[0] : $default;
        return ($val !== null && $val !== '') ? $val : $default;
    };

    $scalarNum = function ($val, $default = 0) {
        if (is_array($val))
            return isset($val[0]) ? $val[0] : $default;
        return ($val !== null && $val !== '') ? $val : $default;
    };

    $vendor_id = $scalar(isset($data['vendor_id']) ? $data['vendor_id'] : '');

    $tds_query = $this->db->select('TDSSection, TDSPer')
                          ->where('AccountID', $vendor_id)
                          ->get('tblclients')
                          ->row();

    $tds_section = $tds_query ? $tds_query->TDSSection : '';
    $tds_per     = $tds_query ? $tds_query->TDSPer : 0;

    $update_data = [
        'PlantID'           => $plant,
        'FY'                => $fy,
        'PurchaseLocation'  => $scalar(isset($data['purchase_location']) ? $data['purchase_location'] : ''),
        'TransDate'         => isset($data['quotation_date']) ? $this->parse_date($scalar($data['quotation_date'])) : null,
        'TransDate2'        => isset($data['quotation_date']) ? $this->parse_date($scalar($data['quotation_date'])) : null,
        'ItemType'          => $scalar(isset($data['item_type']) ? $data['item_type'] : ''),
        'ItemCategory'      => $scalar(isset($data['item_category']) ? $data['item_category'] : (isset($data['order_category']) ? $data['order_category'] : '')),
        'QuatationID'       => $scalar(isset($data['vendor_quote_no']) ? $data['vendor_quote_no'] : ''),
        'AccountID'         => $scalar(isset($data['vendor_id']) ? $data['vendor_id'] : (isset($data['vendor_name']) ? $data['vendor_name'] : '')),
        'BrokerID'          => $scalar(isset($data['broker']) ? $data['broker'] : ''),
        'DeliveryLocation'  => $scalar(isset($data['vendor_location']) ? $data['vendor_location'] : ''),
        'DeliveryFrom'      => isset($data['delivery_from']) ? $this->parse_date($scalar($data['delivery_from'])) : null,
        'DeliveryTo'        => isset($data['delivery_to']) ? $this->parse_date($scalar($data['delivery_to'])) : null,
        'VendorDocNo'       => $scalar(isset($data['vendor_doc_no']) ? $data['vendor_doc_no'] : ''),
        'VendorDocDate'     => isset($data['vendor_doc_date']) ? $this->parse_date($scalar($data['vendor_doc_date'])) : null,
        'PaymentTerms'      => $scalar(isset($data['payment_terms']) ? $data['payment_terms'] : ''),
        'FreightTerms'      => $scalar(isset($data['freight_terms']) ? $data['freight_terms'] : ''),
        'GSTIN'             => $scalar(isset($data['vendor_gst_no']) ? $data['vendor_gst_no'] : ''),
        'TotalWeight'       => $scalarNum(isset($data['total_weight']) ? $data['total_weight'] : 0, 0),
        'TotalQuantity'     => $scalarNum(isset($data['total_qty']) ? $data['total_qty'] : 0, 0),
        'ItemAmt'           => $scalarNum(isset($data['item_total_amt']) ? $data['item_total_amt'] : 0, 0),
        'DiscAmt'           => $scalarNum(isset($data['disc_amt']) ? $data['disc_amt'] : (isset($data['total_disc_amt']) ? $data['total_disc_amt'] : 0), 0),
        'TaxableAmt'        => $scalarNum(isset($data['taxable_amt']) ? $data['taxable_amt'] : 0, 0),
        'CGSTAmt'           => $scalarNum(isset($data['cgst_amt']) ? $data['cgst_amt'] : 0, 0),
        'SGSTAmt'           => $scalarNum(isset($data['sgst_amt']) ? $data['sgst_amt'] : 0, 0),
        'IGSTAmt'           => $scalarNum(isset($data['igst_amt']) ? $data['igst_amt'] : 0, 0),
        'RoundOffAmt'       => $scalarNum(isset($data['round_off_amt']) ? $data['round_off_amt'] : 0, 0),
        'Internal_Remarks'  => $scalar(isset($data['internal_remarks']) ? $data['internal_remarks'] : ''),
        'Document_Remark'   => $scalar(isset($data['document_remark']) ? $data['document_remark'] : ''),
        'Attachment'        => isset($data['attachment']) && !is_array($data['attachment']) ? $data['attachment'] : '',
        'NetAmt'            => $scalarNum(isset($data['net_amt']) ? $data['net_amt'] : 0, 0),
        'UserID'            => $user,
        'TDSSection'        => $tds_section,
        'TDSPercentage'     => $tds_per,
        'Lupdate'           => date('Y-m-d H:i:s'),
        'Status'            => 4,
    ];

    // Update master record
    $this->db->where('PurchID', $data['PurchID']);
    $this->db->update('tblPurchaseOrderMaster', $update_data);

    // =====================
    // ITEMS UPDATE/REPLACE
    // =====================

    // Build items array (items_json  POST arrays )
    $items = [];
    if (isset($data['items_json']) && $data['items_json'] !== '[]' && !empty(trim($data['items_json']))) {
        $decoded = json_decode($data['items_json'], true);
        if (is_array($decoded) && count($decoded) > 0) {
            $items = $decoded;
        }
    }
    if (empty($items) && isset($data['item_id']) && is_array($data['item_id'])) {
        foreach ($data['item_id'] as $index => $item_id) {
            if (empty($item_id))
                continue;
            $items[] = [
                'item_id'     => $item_id,
                'item_uid'    => isset($data['item_uid'][$index]) ? $data['item_uid'][$index] : '0',
                'uom'         => isset($data['uom'][$index]) ? $data['uom'][$index] : '',
                'unit_weight' => isset($data['unit_weight'][$index]) ? $data['unit_weight'][$index] : '0',
                'min_qty'     => isset($data['min_qty'][$index]) ? $data['min_qty'][$index] : '0',
                'max_qty'     => isset($data['max_qty'][$index]) ? $data['max_qty'][$index] : '0',
                'disc_amt'    => isset($data['disc_amt'][$index]) ? $data['disc_amt'][$index] : '0',
                'unit_rate'   => isset($data['unit_rate'][$index]) ? $data['unit_rate'][$index] : '0',
                'gst'         => isset($data['gst'][$index]) ? $data['gst'][$index] : '0',
                'amount'      => isset($data['amount'][$index]) ? $data['amount'][$index] : '0',
            ];
        }
    }

    // =====================================================================
    // QUOTATION MATCH CHECK (Save/Update )
    // QuatationID  tblhistory  existing items fetch 
    // =====================================================================
    $quotation_status = 5; // default: match 

    $quote_no = $scalar(isset($data['vendor_quote_no']) ? $data['vendor_quote_no'] : '');

    if (!empty($quote_no) && !empty($items)) {

        // SELECT ItemID, OrderQty FROM tblhistory WHERE OrderID = QuatationID
        $existing_history = $this->db->select('ItemID, OrderQty')
                                     ->where('OrderID', $quote_no)
                                     ->get('tblhistory')
                                     ->result_array();

        // Existing items  key => value map  convert : [ItemID => OrderQty]
        $existing_map = [];
        foreach ($existing_history as $row) {
            $existing_map[$row['ItemID']] = floatval($row['OrderQty']);
        }

        //   item  match check 
        $all_matched = true;
        if (!empty($existing_map)) {
            foreach ($items as $item) {
                $new_item_id  = isset($item['item_id']) ? $item['item_id'] : '';
                $new_item_qty = isset($item['min_qty']) ? floatval($item['min_qty']) : 0;

                // ItemID  OrderQty  match  
                if (
                    !isset($existing_map[$new_item_id]) ||
                    $existing_map[$new_item_id] != $new_item_qty
                ) {
                    $all_matched = false;
                    break;
                }
            }
        } else {
            // Quotation   items  → match 
            $all_matched = false;
        }

        $quotation_status = $all_matched ? 4 : 5;

        // tblPurchQuotationMaster  status update 
        $this->db->where('QuotatioonID', $quote_no);
        $this->db->update('tblPurchQuotationMaster', ['Status' => $quotation_status]);
    }

    // Remove old items
    $this->db->where('OrderID', $data['PurchID']);
    $this->db->delete('tblhistory');

    // Insert new items
    $ordinal = 1;
    foreach ($items as $item) {
        $qty        = isset($item['min_qty']) ? (float) $item['min_qty'] : 0;
        $rate       = isset($item['unit_rate']) ? (float) $item['unit_rate'] : 0;
        $discAmt    = isset($item['disc_amt']) ? (float) $item['disc_amt'] : 0;
        $gstPercent = isset($item['gst']) ? (float) $item['gst'] : (isset($item['gst_percent']) ? (float) $item['gst_percent'] : 0);
        $amount     = ($rate - $discAmt) * $qty;
        $gstAmt     = $amount * ($gstPercent / 100);

        $cgst = $sgst = $igst = $cgstamt = $sgstamt = $igstamt = 0;
        $vendorState  = isset($data['vendor_state']) ? strtoupper($data['vendor_state']) : '';
        $companyState = isset($data['company_state']) ? strtoupper($data['company_state']) : '';

        if ($vendorState && $companyState && $vendorState === $companyState) {
            $cgst    = $sgst    = $gstPercent / 2;
            $cgstamt = $sgstamt = $gstAmt / 2;
        } else {
            $igst    = $gstPercent;
            $igstamt = $gstAmt;
        }

        $item_insert = [
            'PlantID'       => $plant,
            'FY'            => $fy,
            'OrderID'       => $id,
            'BillID'        => null,
            'TransID'       => null,
            'TransDate'     => isset($data['quotation_date']) ? $this->parse_date($scalar($data['quotation_date'])) : date('Y-m-d H:i:s'),
            'TransDate2'    => date('Y-m-d H:i:s'),
            'TType'         => 'P',
            'TType2'        => 'Order',
            'AccountID'     => $scalar(isset($data['vendor_id']) ? $data['vendor_id'] : ''),
            'ItemID'        => isset($item['item_id']) ? $item['item_id'] : '',
            'GodownID'      => null,
            'Mrp'           => 0,
            'BasicRate'     => $rate,
            'SaleRate'      => $rate,
            'SuppliedIn'    => isset($item['uom']) ? $item['uom'] : '',
            'UnitWeight'    => isset($item['unit_weight']) ? $item['unit_weight'] : 0,
            'WeightUnit'    => '',
            'CaseQty'       => 1.000,
            'OrderQty'      => $qty,
            'eOrderQty'     => null,
            'ereason'       => null,
            'BilledQty'     => $qty,
            'Cases'         => 0.000,
            'DiscPerc'      => 0,
            'DiscAmt'       => $discAmt,
            'cgst'          => $cgst,
            'cgstamt'       => $cgstamt,
            'sgst'          => $sgst,
            'sgstamt'       => $sgstamt,
            'igst'          => $igst,
            'igstamt'       => $igstamt,
            'OrderAmt'      => $amount,
            'ChallanAmt'    => $amount,
            'NetOrderAmt'   => $amount + $cgstamt + $sgstamt + $igstamt,
            'NetChallanAmt' => $amount + $cgstamt + $sgstamt + $igstamt,
            'Ordinalno'     => $ordinal++,
            'UserID'        => $user,
            'UserID2'       => $user,
            'Lupdate'       => date('Y-m-d H:i:s'),
            'batch_no'      => '',
            'expiry_date'   => '',
        ];

        $this->db->insert('tblhistory', $item_insert);
    }

    return true;
}

// ============================================================

public function add_pur_order_po($data)
{
    $plant = $this->session->userdata('root_company');
    $user  = $this->session->userdata('staff_user_id');

    // Helper: safely get scalar value (if array, take first element)
    $scalar = function ($val, $default = '') {
        if (is_array($val))
            return isset($val[0]) ? $val[0] : $default;
        return ($val !== null && $val !== '') ? $val : $default;
    };

    $scalarNum = function ($val, $default = 0) {
        if (is_array($val))
            return isset($val[0]) ? $val[0] : $default;
        return ($val !== null && $val !== '') ? $val : $default;
    };

    // TDS Details fetch  vendor_id 
    $vendor_id = $scalar(isset($data['vendor_id']) ? $data['vendor_id'] : '');

    $tds_query = $this->db->select('TDSSection, TDSPer')
                          ->where('AccountID', $vendor_id)
                          ->get('tblclients')
                          ->row();

    $tds_section = $tds_query ? $tds_query->TDSSection : '';
    $tds_per     = $tds_query ? $tds_query->TDSPer : 0;

    $insert_data = [
        'PlantID'          => $plant,
        'FY'               => $this->session->userdata('finacial_year'),
        'PurchaseLocation' => $scalar(isset($data['purchase_location']) ? $data['purchase_location'] : ''),
        'PurchID'          => $scalar(isset($data['order_no']) ? $data['order_no'] : ''),
        'TransDate'        => isset($data['quotation_date']) ? $this->parse_date($scalar($data['quotation_date'])) : null,
        'TransDate2'       => isset($data['quotation_date']) ? $this->parse_date($scalar($data['quotation_date'])) : null,
        'ItemType'         => $scalar(isset($data['item_type']) ? $data['item_type'] : ''),
        'ItemCategory'     => $scalar(isset($data['item_category']) ? $data['item_category'] : (isset($data['order_category']) ? $data['order_category'] : '')),
        'QuatationID'      => $scalar(isset($data['vendor_quote_no']) ? $data['vendor_quote_no'] : ''),
        'AccountID'        => $scalar(isset($data['vendor_id']) ? $data['vendor_id'] : (isset($data['vendor_name']) ? $data['vendor_name'] : '')),
        'BrokerID'         => $scalar(isset($data['broker']) ? $data['broker'] : ''),
        'DeliveryLocation' => $scalar(isset($data['vendor_location']) ? $data['vendor_location'] : ''),
        'DeliveryFrom'     => isset($data['delivery_from']) ? $this->parse_date($scalar($data['delivery_from'])) : null,
        'DeliveryTo'       => isset($data['delivery_to']) ? $this->parse_date($scalar($data['delivery_to'])) : null,
        'VendorDocNo'      => $scalar(isset($data['vendor_doc_no']) ? $data['vendor_doc_no'] : ''),
        'VendorDocDate'    => isset($data['vendor_doc_date']) ? $this->parse_date($scalar($data['vendor_doc_date'])) : null,
        'PaymentTerms'     => $scalar(isset($data['payment_terms']) ? $data['payment_terms'] : ''),
        'FreightTerms'     => $scalar(isset($data['freight_terms']) ? $data['freight_terms'] : ''),
        'GSTIN'            => $scalar(isset($data['vendor_gst_no']) ? $data['vendor_gst_no'] : ''),
        'TotalWeight'      => $scalarNum(isset($data['total_weight']) ? $data['total_weight'] : 0, 0),
        'TotalQuantity'    => $scalarNum(isset($data['total_qty']) ? $data['total_qty'] : 0, 0),
        'ItemAmt'          => $scalarNum(isset($data['item_total_amt']) ? $data['item_total_amt'] : 0, 0),
        'DiscAmt'          => $scalarNum(isset($data['disc_amt']) ? $data['disc_amt'] : (isset($data['total_disc_amt']) ? $data['total_disc_amt'] : 0), 0),
        'TaxableAmt'       => $scalarNum(isset($data['taxable_amt']) ? $data['taxable_amt'] : 0, 0),
        'CGSTAmt'          => $scalarNum(isset($data['cgst_amt']) ? $data['cgst_amt'] : 0, 0),
        'SGSTAmt'          => $scalarNum(isset($data['sgst_amt']) ? $data['sgst_amt'] : 0, 0),
        'IGSTAmt'          => $scalarNum(isset($data['igst_amt']) ? $data['igst_amt'] : 0, 0),
        'RoundOffAmt'      => $scalarNum(isset($data['round_off_amt']) ? $data['round_off_amt'] : 0, 0),
        'Internal_Remarks' => $scalar(isset($data['internal_remarks']) ? $data['internal_remarks'] : ''),
        'Document_Remark'  => $scalar(isset($data['document_remark']) ? $data['document_remark'] : ''),
        'Attachment'       => isset($data['attachment']) && !is_array($data['attachment']) ? $data['attachment'] : '',
        'NetAmt'           => $scalarNum(isset($data['net_amt']) ? $data['net_amt'] : 0, 0),
        'UserID'           => $user,
        'TDSSection'       => $tds_section,
        'TDSPercentage'    => $tds_per,
        'Lupdate'          => date('Y-m-d H:i:s'),
        'Status'           => 4,
    ];

    // Insert master record
    $this->db->insert('tblPurchaseOrderMaster', $insert_data);
    $insert_id = $this->db->insert_id();

    // =====================================================================
    // ITEMS BUILD - items_json empty   POST arrays  build 
    // =====================================================================
    $items = [];

    if (isset($data['items_json']) && $data['items_json'] !== '[]' && !empty(trim($data['items_json']))) {
        $decoded = json_decode($data['items_json'], true);
        if (is_array($decoded) && count($decoded) > 0) {
            $items = $decoded;
        }
    }

    if (empty($items) && isset($data['item_id']) && is_array($data['item_id'])) {
        foreach ($data['item_id'] as $index => $item_id) {
            if (empty($item_id))
                continue;

            $items[] = [
                'item_id'     => $item_id,
                'hsn_code'    => isset($data['hsn_code'][$index]) ? $data['hsn_code'][$index] : '',
                'uom'         => isset($data['uom'][$index]) ? $data['uom'][$index] : '',
                'unit_weight' => isset($data['unit_weight'][$index]) ? $data['unit_weight'][$index] : 0,
                'min_qty'     => isset($data['min_qty'][$index]) ? $data['min_qty'][$index] : 0,
                'max_qty'     => isset($data['max_qty'][$index]) ? $data['max_qty'][$index] : 0,
                'disc_amt'    => isset($data['disc_amt'][$index]) ? $data['disc_amt'][$index] : 0,
                'disc_Amt'    => isset($data['disc_amt'][$index]) ? $data['disc_amt'][$index] : 0,
                'unit_rate'   => isset($data['unit_rate'][$index]) ? $data['unit_rate'][$index] : 0,
                'gst'         => isset($data['gst'][$index]) ? $data['gst'][$index] : 0,
                'gst_percent' => isset($data['gst'][$index]) ? $data['gst'][$index] : 0,
                'amount'      => isset($data['amount'][$index]) ? $data['amount'][$index] : 0,
            ];
        }
    }

    // =====================================================================
    // QUOTATION MATCH CHECK (Insert )
    // SELECT ItemID, OrderQty FROM tblhistory WHERE OrderID = QuatationID
    // =====================================================================
    $quote_no = $scalar(isset($data['vendor_quote_no']) ? $data['vendor_quote_no'] : '');

    if (!empty($quote_no) && !empty($items)) {

        // Quotation  OrderID  existing history items fetch 
        $existing_history = $this->db->select('ItemID, OrderQty')
                                     ->where('OrderID', $quote_no)
                                     ->get('tblhistory')
                                     ->result_array();

        // Existing items  map  convert : [ItemID => OrderQty]
        $existing_map = [];
        foreach ($existing_history as $row) {
            $existing_map[$row['ItemID']] = floatval($row['OrderQty']);
        }

        //   item  ItemID + OrderQty match check 
        $all_matched = true;
        if (!empty($existing_map)) {
            foreach ($items as $item) {
                $new_item_id  = isset($item['item_id']) ? $item['item_id'] : '';
                $new_item_qty = isset($item['min_qty']) ? floatval($item['min_qty']) : 0;

                // ItemID  map   Qty  → match 
                if (
                    !isset($existing_map[$new_item_id]) ||
                    $existing_map[$new_item_id] != $new_item_qty
                ) {
                    $all_matched = false;
                    break;
                }
            }
        } else {
            // Quotation history  → match 
            $all_matched = false;
        }

        // Status set : Match → 4, No Match → 5
        $quotation_status = $all_matched ? 4 : 5;

        $this->db->where('QuotatioonID', $quote_no);
        $this->db->update('tblPurchQuotationMaster', ['Status' => $quotation_status]);

    } elseif (!empty($quote_no)) {
        // vendor_quote_no   items  → status 5
        $this->db->where('QuotatioonID', $quote_no);
        $this->db->update('tblPurchQuotationMaster', ['Status' => 5]);
    }

    // =====================================================================
    // ITEMS INSERT INTO tblhistory
    // =====================================================================
    $i = 1;
    $all_history = [];

    foreach ($items as $item) {

        $vendor_state = isset($data['vendor_state']) ? strtoupper(trim($data['vendor_state'])) : '';

        $gst_percent = 0;
        if (isset($item['gst_percent'])) {
            $gst_percent = floatval($item['gst_percent']);
        } elseif (isset($item['gst'])) {
            $gst_percent = floatval($item['gst']);
        }

        $disc_amt = 0;
        if (isset($item['disc_Amt'])) {
            $disc_amt = floatval($item['disc_Amt']);
        } elseif (isset($item['disc_amt'])) {
            $disc_amt = floatval($item['disc_amt']);
        }

        $unit_rate   = isset($item['unit_rate']) ? floatval($item['unit_rate']) : 0;
        $min_qty     = isset($item['min_qty']) ? floatval($item['min_qty']) : 0;
        $taxable_amt = ($unit_rate - $disc_amt) * $min_qty;

        $cgst = $sgst = $cgstamt = $sgstamt = $igst = $igstamt = 0;

        if ($vendor_state === 'MAHARASHTRA') {
            $cgst    = $sgst    = $gst_percent / 2;
            $cgstamt = $sgstamt = ($taxable_amt * $cgst) / 100;
        } else {
            $igst    = $gst_percent;
            $igstamt = ($taxable_amt * $igst) / 100;
        }

        $history_data = [
            'OrderID'       => isset($data['order_no']) ? $data['order_no'] : '',
            'PlantID'       => $plant,
            'FY'            => $this->session->userdata('finacial_year'),
            'TransDate'     => isset($data['quotation_date'])
                                ? $this->parse_date($data['quotation_date'])
                                : (isset($data['delivery_from']) ? $this->parse_date($data['delivery_from']) : null),
            'TransDate2'    => date('Y-m-d H:i:s'),
            'TType'         => 'PO',
            'TType2'        => 'Order',
            'AccountID'     => isset($data['vendor_id']) ? $data['vendor_id'] : (isset($data['vendor_name']) ? $data['vendor_name'] : ''),
            'ItemID'        => isset($item['item_id']) ? $item['item_id'] : '',
            'GodownID'      => '',
            'Mrp'           => 0,
            'BasicRate'     => $unit_rate,
            'SaleRate'      => ($unit_rate * $gst_percent) / 100,
            'SuppliedIn'    => isset($item['uom']) ? $item['uom'] : '',
            'UnitWeight'    => isset($item['unit_weight']) ? $item['unit_weight'] : 0,
            'WeightUnit'    => isset($item['uom']) ? $item['uom'] : '',
            'CaseQty'       => 1.000,
            'OrderQty'      => $min_qty,
            'eOrderQty'     => $min_qty,
            'ereason'       => '',
            'BilledQty'     => 0,
            'Cases'         => 0.000,
            'DiscPerc'      => 0,
            'DiscAmt'       => $disc_amt,
            'cgst'          => $cgst,
            'cgstamt'       => $cgstamt,
            'sgst'          => $sgst,
            'sgstamt'       => $sgstamt,
            'igst'          => $igst,
            'igstamt'       => $igstamt,
            'OrderAmt'      => $min_qty * $unit_rate,
            'ChallanAmt'    => 0,
            'NetOrderAmt'   => isset($item['amount']) ? $item['amount'] : 0,
            'NetChallanAmt' => 0,
            'Ordinalno'     => $i,
            'UserID'        => $user,
            'UserID2'       => '',
            'Lupdate'       => date('Y-m-d H:i:s'),
            'batch_no'      => '',
            'expiry_date'   => '',
        ];

        $this->db->insert('tblhistory', $history_data);

        if ($this->db->affected_rows() == 0) {
            log_message('error', 'tblhistory insert failed: ' . print_r($history_data, true) . ' DB Error: ' . print_r($this->db->error(), true));
        }

        $all_history[] = $history_data;
        $i++;
    }

    return $insert_id;
}

	// Helper to parse DD/MM/YYYY to Y-m-d
	private function parse_date($date_str)
	{
		if (!$date_str)
			return null;
		$parts = explode('/', $date_str);
		if (count($parts) === 3) {
			return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
		}
		return $date_str;
	}

	public function increment_next_purchase_order_number()
	{

		// Update next TAX Transaction number in settings

		$FY = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');

		if ($selected_company == 1) {

			$this->db->where('name', 'next_purchase_order_number_for_cspl');



		} elseif ($selected_company == 2) {

			$this->db->where('name', 'next_purchase_order_number_for_cff');



		} elseif ($selected_company == 3) {

			$this->db->where('name', 'next_purchase_order_number_for_cbu');



		}



		$this->db->set('value', 'value+1', false);

		$this->db->WHERE('FY', $FY);

		$this->db->update(db_prefix() . 'options');

	}





	public function load_data_for_purchaseOrder($data)
	{

		$from_date = to_sql_date($data["from_date"]);

		$to_date = to_sql_date($data["to_date"]);

		$status = $data["status"];

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'purchaseordermaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'purchaseordermaster.FY = "' . $fy . '" AND ' . db_prefix() . 'purchaseordermaster.PlantID = "' . $selected_company . '"';

		if (!empty($status)) {

			$sql1 .= ' AND ' . db_prefix() . 'purchaseordermaster.cur_status = "' . $status . '"';

		}

		$sql1 .= ' ORDER BY Transdate DESC';



		$sql = 'SELECT ' . db_prefix() . 'purchaseordermaster.*,  

			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchaseordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . $selected_company . ') as AccountName

			FROM ' . db_prefix() . 'purchaseordermaster WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}



	public function load_data_for_PendingpurchaseOrder($data)
	{

		$from_date = to_sql_date($data["from_date"]);

		$to_date = to_sql_date($data["to_date"]);

		$status = $data["status"];

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'purchaseordermaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'purchaseordermaster.FY = "' . $fy . '" AND ' . db_prefix() . 'purchaseordermaster.PlantID = "' . $selected_company . '"';



		if (!empty($status)) {

			$sql1 .= ' AND ' . db_prefix() . 'purchaseordermaster.cur_status = "' . $status . '"';

		} else {

			$sql1 .= ' AND ' . db_prefix() . 'purchaseordermaster.cur_status IN ("Approved","InProgress")';

		}

		$sql1 .= ' ORDER BY Transdate DESC';



		$sql = 'SELECT ' . db_prefix() . 'purchaseordermaster.*,  

			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchaseordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . $selected_company . ') as AccountName, 

			(SELECT COALESCE(SUM(BilledQty),0) FROM ' . db_prefix() . 'history WHERE ' . db_prefix() . 'history.OrderID = ' . db_prefix() . 'purchaseordermaster.PurchID AND ' . db_prefix() . 'history.PlantID = ' . $selected_company . ' AND ' . db_prefix() . 'history.TType = "P" AND ' . db_prefix() . 'history.TType2 = "Order") as OrderQty,

			(SELECT COALESCE(SUM(BilledQty),0) FROM ' . db_prefix() . 'history WHERE ' . db_prefix() . 'history.BillID = ' . db_prefix() . 'purchaseordermaster.PurchID AND ' . db_prefix() . 'history.PlantID = ' . $selected_company . ' AND ' . db_prefix() . 'history.TType = "P" AND ' . db_prefix() . 'history.TType2 = "Purchase") as ReceivedQty

			FROM ' . db_prefix() . 'purchaseordermaster WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}

	public function load_data_for_PendingpurchaseOrderItemWise($data)
	{

		$from_date = to_sql_date($data["from_date"]);

		$to_date = to_sql_date($data["to_date"]);

		$status = $data["status"];

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'purchaseordermaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'purchaseordermaster.FY = "' . $fy . '" AND ' . db_prefix() . 'purchaseordermaster.PlantID = "' . $selected_company . '"';



		if (!empty($status)) {

			$sql1 .= ' AND ' . db_prefix() . 'purchaseordermaster.cur_status = "' . $status . '"';

		} else {

			$sql1 .= ' AND ' . db_prefix() . 'purchaseordermaster.cur_status IN ("Approved","InProgress")';

		}

		$sql1 .= ' ORDER BY Transdate DESC';



		$sql = 'SELECT ' . db_prefix() . 'purchaseordermaster.*,tblitems.description,DataHistory.BilledQty as OrderQty,DataHistory.Delivery_Date,DataHistory.NewDelivery_Date,DataHistory.ItemID,  

			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchaseordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . $selected_company . ') as AccountName,

			(SELECT COALESCE(SUM(BilledQty),0) FROM ' . db_prefix() . 'history WHERE ' . db_prefix() . 'history.BillID = ' . db_prefix() . 'purchaseordermaster.PurchID AND ' . db_prefix() . 'history.ItemID = DataHistory.ItemID AND ' . db_prefix() . 'history.PlantID = ' . $selected_company . ' AND ' . db_prefix() . 'history.TType = "P" AND ' . db_prefix() . 'history.TType2 = "Purchase") as ReceivedQty

			FROM ' . db_prefix() . 'history as DataHistory

			INNER JOIN tblpurchaseordermaster ON tblpurchaseordermaster.PurchID = DataHistory.OrderID

			INNER JOIN tblitems ON tblitems.ItemID = DataHistory.ItemID

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}



	public function ItemDateExtension($data)
	{

		$update_array = array(

			'NewDelivery_Date' => to_sql_date($data["extension_date"]),

			'Delivery_Date_Remark' => $data["extension_remark"],

			'UserID2' => $this->session->userdata('username'),

			'Lupdate' => date('Y-m-d H:i:s'),

		);

		$this->db->where('ItemID', $data["ItemID"]);

		$this->db->where('OrderID', $data["PurchID"]);

		$this->db->where('TType', 'P');

		$this->db->where('TType2', 'Order');

		$this->db->update(db_prefix() . 'history', $update_array);

		if ($this->db->affected_rows() > 0) {

			return true;

		} else {

			return false;

		}

	}



	public function get_purchase_order_master_data($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $this->session->userdata('finacial_year');

		$this->db->select('tblpurchaseordermaster.*,tblpurchaseordermaster.AccountID As Vendor,tblclients.*,tblxx_statelist.*,tblaccountbalances.*,tblxx_citylist.city_name,tblTDSMaster.TDSName,tblTDSMaster.TDSCode');

		$this->db->from(db_prefix() . 'purchaseordermaster');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchaseordermaster.AccountID', 'left');

		$this->db->join(db_prefix() . 'TDSMaster', db_prefix() . 'TDSMaster.TDSCode = ' . db_prefix() . 'clients.TDSSection', 'left');

		$this->db->join(db_prefix() . 'xx_statelist', db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.state', 'left');

		$this->db->join(db_prefix() . 'xx_citylist', db_prefix() . 'xx_citylist.id = ' . db_prefix() . 'clients.city', 'left');

		$this->db->join(db_prefix() . 'accountbalances', db_prefix() . 'accountbalances.AccountID = ' . db_prefix() . 'clients.AccountID AND ' . db_prefix() . 'accountbalances.PlantID = ' . db_prefix() . 'clients.PlantID AND ' . db_prefix() . 'accountbalances.FY ="' . $year . '"', 'left');

		$this->db->where(db_prefix() . 'purchaseordermaster.PurchID', $id);

		$this->db->where(db_prefix() . 'purchaseordermaster.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'purchaseordermaster.FY', $year);

		$data = $this->db->get()->row();

		if ($data->ShipToParty) {

			$ShipToAddress = $this->GetShippingAddress($data->ShipToParty);

			$data->ShipToAddressList = $ShipToAddress;

		}

		if ($data) {

			$data->items = $this->ItemAssocToVendor($data->AccountID);

		}

		return $data;

	}







	public function ApprovePO($id)
	{



		$selected_company = $this->session->userdata('root_company');

		$update_arr = [

			'cur_status' => 'Approved',

		];

		$this->db->where('PlantID', $selected_company);

		$this->db->where('PurchID', $id);

		if ($this->db->update(db_prefix() . 'purchaseordermaster', $update_arr)) {



			return true;

		} else {

			return false;

		}

	}

	public function CompletePendingOrder($id)
	{



		$selected_company = $this->session->userdata('root_company');

		$update_arr = [

			'cur_status' => 'Completed',

		];

		$this->db->where('PlantID', $selected_company);

		$this->db->where('PurchID', $id);

		if ($this->db->update(db_prefix() . 'purchaseordermaster', $update_arr)) {



			return true;

		} else {

			return false;

		}

	}





	public function get_p_order_detail_old_item($itemid)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select(db_prefix() . 'history.*,tblclients.company');

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID', 'left');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID', 'left');

		$this->db->where(db_prefix() . 'history.TType', 'P');

		$this->db->where(db_prefix() . 'history.TType2', 'Purchase');

		$this->db->where(db_prefix() . 'history.ItemID', $itemid);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		$this->db->order_by(db_prefix() . 'history.TransDate', 'DESC');

		$this->db->limit(3);

		$data = $this->db->get()->result_array();



		return $data;



	}

	public function get_last_purch_order_detail_old_item($itemid)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select(db_prefix() . 'history.*,tblclients.company');

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID', 'left');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID', 'left');

		$this->db->where(db_prefix() . 'history.TType', 'P');

		$this->db->where(db_prefix() . 'history.TType2', 'Purchase');

		$this->db->where(db_prefix() . 'history.ItemID', $itemid);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);

		$this->db->order_by(db_prefix() . 'history.TransDate', 'DESC');

		$this->db->limit(1);

		$data = $this->db->get()->row();



		return $data;



	}





	public function pendingOrder_list()
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'purchaseordermaster');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchaseordermaster.AccountID', 'left');

		//  $this->db->where(db_prefix() . 'clients.userid', $id);

		$this->db->where(db_prefix() . 'purchaseordermaster.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'purchaseordermaster.FY', $year);

		$this->db->where(db_prefix() . 'purchaseordermaster.cur_status', 'Approved');

		$this->db->order_by(db_prefix() . 'purchaseordermaster.PurchID', "DESC");

		return $this->db->get()->result_array();

	}

	public function pendingOrder_list_ByVendor($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select('tblpurchaseordermaster.*');

		$this->db->from(db_prefix() . 'purchaseordermaster');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchaseordermaster.AccountID', 'left');

		$this->db->where(db_prefix() . 'clients.AccountID', $id);

		$this->db->where(db_prefix() . 'purchaseordermaster.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'clients.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'purchaseordermaster.FY', $year);

		$this->db->where_in(db_prefix() . 'purchaseordermaster.cur_status', ['Approved', 'InProgress']);

		$this->db->order_by(db_prefix() . 'purchaseordermaster.PurchID', "DESC");

		return $this->db->get()->result_array();

	}



	public function GetItemStock($itemId)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		if ($selected_company == "1") {

			$GodownID = 'CSPL';

		} else if ($selected_company == "2") {

			$GodownID = 'CFF';

		} else if ($selected_company == "3") {

			$GodownID = 'CBUPL';

		}

		$this->db->select('tblitems.*,COALESCE(tblstockmaster.OQty,0) AS OQty');

		$this->db->from(db_prefix() . 'items');

		$this->db->join(db_prefix() . 'stockmaster', db_prefix() . 'stockmaster.ItemID = ' . db_prefix() . 'items.ItemID AND ' . db_prefix() . 'stockmaster.PlantID = ' . db_prefix() . 'items.PlantID AND ' . db_prefix() . 'stockmaster.FY = ' . $fy . '', 'left');

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items.ItemID', $itemId);

		$Itemsdata = $this->db->get()->row();



		$this->db->select('ItemID,TType,TType2,SUM(BilledQty) AS BilledQty');

		$this->db->from(db_prefix() . 'history');

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.GodownID', $GodownID);

		$this->db->where(db_prefix() . 'history.BillID is NOT NULL', NULL, FALSE);

		$this->db->where(db_prefix() . 'history.FY', $fy);

		$this->db->where(db_prefix() . 'history.ItemID', $itemId);

		$this->db->group_by('ItemID,TType,TType2');

		$data = $this->db->get()->result_array();

		$PQty = 0;

		$PRQty = 0;

		$IQty = 0;

		$PRDQty = 0;

		$SQty = 0;

		$SRTQty = 0;

		$AQty = 0;

		$AQty2 = 0;

		$AQty3 = 0;

		$AQty4 = 0;

		$GIQty = 0;

		$GOQty = 0;





		foreach ($data as $stock) {

			if ($stock['TType'] == 'P' && $stock['TType2'] == 'Purchase') {

				$PQty = $stock['BilledQty'];

			} elseif ($stock['TType'] == 'N') {

				$PRQty = $stock['BilledQty'];

			} elseif ($stock['TType'] == 'A') {

				$IQty = $stock['BilledQty'];

			} elseif ($stock['TType'] == 'B') {

				$PRDQty = $stock['BilledQty'];

			} elseif ($stock['TType'] == 'O' && $stock['TType2'] == 'Order') {

				$SQty = $stock['BilledQty'];

			} elseif ($stock['TType'] == 'R' && $stock['TType2'] == 'Fresh') {

				$SRTQty = $stock['BilledQty'];

			} elseif ($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Adjustment') {

				$AQty += $stock['BilledQty'];

			} elseif ($stock['TType'] == 'X' && $stock['TType2'] == 'Free distribution') {

				$AQty += $stock['BilledQty'];

			} elseif ($stock['TType'] == 'X' && $stock['TType2'] == 'Free Distribution') {

				$AQty += $stock['BilledQty'];

			} elseif ($stock['TType'] == 'X' && $stock['TType2'] == 'Stock Damaged') {

				$AQty += $stock['BilledQty'];

			} elseif ($stock['TType'] == 'X' && $stock['TType2'] == 'Promotional Activity') {

				$AQty += $stock['BilledQty'];

			} elseif ($stock['TType'] == 'T' && $stock['TType2'] == 'In') {

				$GIQty = $stock['BilledQty'];

			} elseif ($stock['TType'] == 'T' && $stock['TType2'] == 'Out') {

				$GOQty = $stock['BilledQty'];

			}

		}

		$stockQty = $Itemsdata->OQty + $PQty - $PRQty - $IQty + $PRDQty - $SQty + $SRTQty - $AQty - $GOQty + $GIQty;

		$stockQtyInCase = $stockQty / $Itemsdata->case_qty;

		return number_format($stockQtyInCase, 2, '.', '');

	}



	public function GetPlantDetails()
	{

		$selected_company = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');



		$sql = 'SELECT ' . db_prefix() . 'setup.*

			FROM ' . db_prefix() . 'setup WHERE PlantID = ' . $selected_company . ' AND FY = "' . $FY . '"';

		$result = $this->db->query($sql)->result();

		$this->db->order_by('tblxx_statelist.state_name', 'ASC');

		return $this->db->get('tblxx_statelist')->result_array();

	}

	//===================== Get Item Group List By Main GroupID ====================

	public function GetItemGroupList()
	{

		$this->db->select('tblitemsSubGroup2.*');

		$this->db->where_in('main_DivisionID', ['2', '3']);

		$this->db->order_by('tblitemsSubGroup2.name', 'ASC');

		return $this->db->get('tblitemsSubGroup2')->result_array();

	}



	public function update_QC_Data($data, $id)
	{



		if (isset($data['QC_detail'])) {

			$QC_detail = json_decode($data['QC_detail']);

			unset($data['QC_detail']);

			$es_detail = [];

			$header = [];



			$header[] = 'para_id';

			$header[] = 'parameter_name';

			$header[] = 'unit_name';

			$header[] = 'min_range';

			$header[] = 'max_range';

			$header[] = 'report_value';

			foreach ($QC_detail as $key => $value) {



				if ($value[0] != '' && $value[5] != '') {

					$es_detail[] = array_combine($header, $value);

				}

			}

		}

		$PlantID = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');



		$Transdate = date('Y-m-d H:i:s');



		$new_qc_no = get_option('next_QC_entry_number_for_cspl');

		$next_qc_no = "IQC" . $FY . $new_qc_no;



		if (count($es_detail) > 0) {

			$date = to_sql_date($data['TransDate']) . " " . date('H:i:s');



			if (!empty($id)) {

				$update_array = array(

					'TransDate' => $date,

					'UserID2' => $_SESSION['username'],

					'Lupdate' => date('Y-m-d H:i:s'),



				);



				$this->db->where('QC_no', $id);

				$this->db->update(db_prefix() . 'qc_item`', $update_array);





				$i = 1;

				foreach ($es_detail as $value) {

					$data_array_result = array(

						'report_value' => $value['report_value'],

						'TransDate' => $date,

						'UserID2' => $_SESSION['username'],

						'lupdate' => date('Y-m-d H:i:s'),

					);



					$this->db->where('QC_no', $id);

					$this->db->where('para_id', $value['para_id']);

					$this->db->update(db_prefix() . 'qc_item_detail', $data_array_result);

				}

				return true;



			} else {

				$data_array = array(

					'QC_no ' => $next_qc_no,

					'TransDate' => $date,

					'PONumber' => $data['PO_number'],

					'ItemID' => $data['item'],

					'UserID' => $_SESSION['username'],

					'TransDate2' => date('Y-m-d H:i:s'),

				);



				$this->db->insert(db_prefix() . 'qc_item', $data_array);

				if ($this->db->affected_rows() > 0) {



					// Update next Inspection number in settings

					$next_number = $new_qc_no + 1;

					$this->db->where('name', 'next_QC_entry_number_for_cspl');

					$this->db->where('FY', $FY);

					$this->db->update(db_prefix() . 'options', ['value' => $next_number]);



					// Update Data In Detail Table	

					$i = 1;

					foreach ($es_detail as $value) {

						$data_array_result = array(

							'QC_no ' => $next_qc_no,

							'TransDate' => $date,

							'PONumber' => $data['PO_number'],

							'ItemID' => $data['item'],

							'para_id' => $value['para_id'],

							'report_value' => $value['report_value'],

							'TransDate' => $date,

							'TransDate2' => date('Y-m-d H:i:s'),

							'UserID' => $_SESSION['username'],

						);



						$this->db->insert(db_prefix() . 'qc_item_detail', $data_array_result);

					}

					return true;

				} else {



					return false;

				}

			}

		} else {

			return false;

		}



	}



	function get_Total_Inspection_Done_PO()
	{

		$status = array('Completed', 'Cancel');

		$this->db->select(db_prefix() . 'purchasemaster.*,tblclients.company,tblclients.phonenumber,tblclients.state');

		$this->db->from(db_prefix() . 'purchasemaster');

		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasemaster.AccountID', 'Inner');

		$this->db->where_not_in(db_prefix() . 'purchasemaster.cur_status ', $status);

		return $this->db->get()->result_array();

	}



	public function GetQCData_byQCno($qc_no)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select('qc_item.*,tblclients.company,tblclients.AccountID,tblclients.phonenumber,tblclients.state');

		$this->db->from(db_prefix() . 'qc_item');

		$this->db->join(db_prefix() . 'purchasemaster', '' . db_prefix() . 'purchasemaster.PurchID = ' . db_prefix() . 'qc_item.PONumber', 'INNER');

		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasemaster.AccountID', 'Inner');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'qc_item.ItemID', 'INNER');

		$this->db->where(db_prefix() . 'qc_item.QC_no', $qc_no);

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		return $this->db->get()->row();

	}



	public function get_Total_Inspection_Done_By_PO_Item($id, $item)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID', 'left');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID', 'left');

		// $this->db->join(db_prefix() . 'history', db_prefix() . 'history.OrderID = ' . db_prefix() . 'purchasemaster.PurchID', 'left');

		$this->db->where_in(db_prefix() . 'history.OrderID', $id);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items.ItemID', $item);

		$this->db->where(db_prefix() . 'history.FY', $year);

		return $this->db->get()->result_array();

	}



	public function GetQCParameterByItem_QCNo($QC_No, $itemid)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select('tblqc_item_detail.report_value,tblqc_master.*,tblqc_parameter.parameter_name,tblqc_unit.unit_name,tblqc_unit.measured_in');

		$this->db->from(db_prefix() . 'qc_item_detail');

		$this->db->join(db_prefix() . 'qc_master', db_prefix() . 'qc_master.para_id = ' . db_prefix() . 'qc_item_detail.para_id', 'INNER');

		$this->db->join(db_prefix() . 'qc_parameter', db_prefix() . 'qc_parameter.id = ' . db_prefix() . 'qc_master.para_id', 'INNER');

		$this->db->join(db_prefix() . 'qc_unit', db_prefix() . 'qc_unit.id = ' . db_prefix() . 'qc_parameter.unit_id', 'INNER');

		$this->db->where(db_prefix() . 'qc_master.ItemID', $itemid);

		$this->db->where(db_prefix() . 'qc_item_detail.QC_no', $QC_No);

		return $this->db->get()->result_array();

	}



	public function get_unique_history_QC($id)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'history');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID', 'left');

		$this->db->join(db_prefix() . 'qc_item', db_prefix() . 'qc_item.ItemID = ' . db_prefix() . 'items.ItemID', 'left');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID', 'left');

		// $this->db->join(db_prefix() . 'history', db_prefix() . 'history.OrderID = ' . db_prefix() . 'purchasemaster.PurchID', 'left');

		$this->db->where_in(db_prefix() . 'history.OrderID', $id);

		$this->db->where(db_prefix() . 'history.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'items.PlantID', $selected_company);

		$this->db->where(db_prefix() . 'history.FY', $year);



		return $this->db->get()->result_array();

	}



	function get_Total_Inspection_Done_By_PO($PurchID)
	{



		$this->db->select(db_prefix() . 'purchasemaster.*,tblclients.company,tblclients.phonenumber,tblclients.state');

		$this->db->from(db_prefix() . 'purchasemaster');

		$this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasemaster.AccountID', 'Inner');

		$this->db->where(db_prefix() . 'purchasemaster.PurchID', $PurchID);

		return $this->db->get()->row();

	}







	public function GetQC_CompleteByItem_PO($PO_No, $itemid)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select('*');

		$this->db->from(db_prefix() . 'qc_item');

		$this->db->where(db_prefix() . 'qc_item.PONumber', $PO_No);

		$this->db->where(db_prefix() . 'qc_item.ItemID', $itemid);

		return $this->db->get()->result_array();

	}



	public function load_data_for_qc_entry($data)
	{

		$selected_company = $this->session->userdata('root_company');

		$from_date = to_sql_date($data["from_date"]);

		$to_date = to_sql_date($data["to_date"]);



		$sql1 = db_prefix() . 'qc_item.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59"';

		$sql1 .= ' AND ' . db_prefix() . 'items.PlantID = "' . $selected_company . '"';

		$sql1 .= '  ORDER BY QC_no DESC';



		$sql = 'SELECT ' . db_prefix() . 'qc_item.*,tblitems.description

			FROM ' . db_prefix() . 'qc_item

			JOIN tblitems ON tblitems.ItemID = tblqc_item.ItemID

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}



	public function update_FG_Test_Data($data, $id)
	{



		if (isset($data['QC_detail'])) {

			$QC_detail = json_decode($data['QC_detail']);

			unset($data['QC_detail']);

			$es_detail = [];

			$header = [];



			$header[] = 'ItemID';

			$header[] = 'batch_no';

			$header[] = 'taste';

			$header[] = 'smell';

			$header[] = 'appearance';

			$header[] = 'moisture';

			// $header[] = 'ash';

			// $header[] = 'salt';

			// $header[] = 'f_m';

			$header[] = 'sign';

			$header[] = 'remark';

			foreach ($QC_detail as $key => $value) {



				if ($value[0] != '' && $value[1] != '') {

					$es_detail[] = array_combine($header, $value);

				}

			}

		}

		$PlantID = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');



		$Transdate = date('Y-m-d H:i:s');



		$new_qc_no = get_option('next_QC_FG_Test_number_for_cspl');

		$next_qc_no = "FTR" . $FY . $new_qc_no;



		if (count($es_detail) > 0) {

			$date = to_sql_date($data['TransDate']) . " " . date('H:i:s');



			if (!empty($id)) {

				$update_array = array(

					'TransDate' => $date,

					'UserID2' => $_SESSION['username'],

					'Lupdate' => date('Y-m-d H:i:s'),



				);



				$this->db->where('entry_no', $id);

				$this->db->update(db_prefix() . 'fg_test_report`', $update_array);





				$i = 1;

				foreach ($es_detail as $value) {

					$data_array_result = array(

						'ItemID' => $value['ItemID'],

						'batch_no' => $value['batch_no'],

						'taste' => $value['taste'],

						'smell' => $value['smell'],

						'appearance' => $value['appearance'],

						'moisture' => $value['moisture'],

						// 'ash'=>$value['ash'],

						// 'salt'=>$value['salt'],

						// 'f_m'=>$value['f_m'],

						'sign' => $value['sign'],

						'remark' => $value['remark'],

						'TransDate' => $date,

						'UserID2' => $_SESSION['username'],

						'lupdate' => date('Y-m-d H:i:s'),

					);



					$this->db->where('entry_no', $id);

					$this->db->where('ItemID', $value['ItemID']);

					$this->db->update(db_prefix() . 'fg_test_report_detail', $data_array_result);

				}

				return true;



			} else {

				$data_array = array(

					'entry_no ' => $next_qc_no,

					'TransDate' => $date,

					'UserID' => $_SESSION['username'],

					'TransDate2' => date('Y-m-d H:i:s'),

				);



				$this->db->insert(db_prefix() . 'fg_test_report', $data_array);

				if ($this->db->affected_rows() > 0) {



					// Update next Inspection number in settings

					$next_number = $new_qc_no + 1;

					$this->db->where('name', 'next_QC_FG_Test_number_for_cspl');

					$this->db->where('FY', $FY);

					$this->db->update(db_prefix() . 'options', ['value' => $next_number]);



					// Update Data In Detail Table	

					$i = 1;

					foreach ($es_detail as $value) {

						$data_array_result = array(

							'entry_no ' => $next_qc_no,

							'TransDate' => $date,

							'ItemID' => $value['ItemID'],

							'batch_no' => $value['batch_no'],

							'taste' => $value['taste'],

							'smell' => $value['smell'],

							'appearance' => $value['appearance'],

							'moisture' => $value['moisture'],

							// 'ash'=>$value['ash'],

							// 'salt'=>$value['salt'],

							// 'f_m'=>$value['f_m'],

							'sign' => $value['sign'],

							'remark' => $value['remark'],

							'TransDate2' => date('Y-m-d H:i:s'),

							'UserID' => $_SESSION['username'],

						);



						$this->db->insert(db_prefix() . 'fg_test_report_detail', $data_array_result);

					}

					return true;

				} else {



					return false;

				}

			}

		} else {

			return false;

		}



	}



	public function GetFgTestData_byentry($entry_no)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'fg_test_report`');



		$this->db->where(db_prefix() . 'fg_test_report`.entry_no', $entry_no);

		return $this->db->get()->row();

	}



	public function GetFgTestDetail_byentry($entry_no)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'fg_test_report_detail');

		$this->db->where(db_prefix() . 'fg_test_report_detail.entry_no', $entry_no);

		return $this->db->get()->result_array();

	}



	public function get_items_code_qc()
	{

		$selected_company = $this->session->userdata('root_company');

		//   $year = $_SESSION['finacial_year'];

		return $this->db->query('select ItemID as id, CONCAT(ItemID," - ",description) as label,ItemID from ' . db_prefix() . 'items where PlantID = ' . $selected_company)->result_array();

	}



	public function update_In_process_plant_Data($data, $id)
	{



		if (isset($data['QC_detail'])) {

			$QC_detail = json_decode($data['QC_detail']);

			unset($data['QC_detail']);

			$es_detail = [];

			$header = [];



			$header[] = 'time';

			$header[] = 'ItemID';

			$header[] = 'fryer_no';

			$header[] = 'ffa_value';

			$header[] = 'tpc_value';

			$header[] = 'remark';

			foreach ($QC_detail as $key => $value) {



				if ($value[0] != '' && $value[1] != '') {

					$es_detail[] = array_combine($header, $value);

				}

			}

		}

		$PlantID = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');



		$Transdate = date('Y-m-d H:i:s');



		$new_qc_no = get_option('next_QC_In_process_plant_number_for_cspl');

		$next_qc_no = "IPP" . $FY . $new_qc_no;



		if (count($es_detail) > 0) {

			$date = to_sql_date($data['TransDate']) . " " . date('H:i:s');



			if (!empty($id)) {

				$update_array = array(

					'TransDate' => $date,

					'UserID2' => $_SESSION['username'],

					'Lupdate' => date('Y-m-d H:i:s'),



				);



				$this->db->where('entry_no', $id);

				$this->db->update(db_prefix() . 'inprocess_plant`', $update_array);





				$i = 1;

				foreach ($es_detail as $value) {

					$data_array_result = array(

						'time' => $value['time'],

						'ItemID' => $value['ItemID'],

						'fryer_pan' => $value['fryer_no'],

						'ffa_value' => $value['ffa_value'],

						'tpc_value' => $value['tpc_value'],

						'remark' => $value['remark'],

						'TransDate' => $date,

						'UserID2' => $_SESSION['username'],

						'lupdate' => date('Y-m-d H:i:s'),

					);



					$this->db->where('entry_no', $id);

					$this->db->where('ItemID', $value['ItemID']);

					$this->db->update(db_prefix() . 'inprocess_plant_detail', $data_array_result);

				}

				return true;



			} else {

				$data_array = array(

					'entry_no ' => $next_qc_no,

					'TransDate' => $date,

					'UserID' => $_SESSION['username'],

					'TransDate2' => date('Y-m-d H:i:s'),

				);



				$this->db->insert(db_prefix() . 'inprocess_plant', $data_array);

				if ($this->db->affected_rows() > 0) {



					// Update next Inspection number in settings

					$next_number = $new_qc_no + 1;

					$this->db->where('name', 'next_QC_In_process_plant_number_for_cspl');

					$this->db->where('FY', $FY);

					$this->db->update(db_prefix() . 'options', ['value' => $next_number]);



					// Update Data In Detail Table	

					$i = 1;

					foreach ($es_detail as $value) {

						$data_array_result = array(

							'entry_no ' => $next_qc_no,

							'TransDate' => $date,

							'time' => $value['time'],

							'ItemID' => $value['ItemID'],

							'fryer_pan' => $value['fryer_no'],

							'ffa_value' => $value['ffa_value'],

							'tpc_value' => $value['tpc_value'],

							'remark' => $value['remark'],

							'TransDate2' => date('Y-m-d H:i:s'),

							'UserID' => $_SESSION['username'],

						);



						$this->db->insert(db_prefix() . 'inprocess_plant_detail', $data_array_result);

					}

					return true;

				} else {



					return false;

				}

			}

		} else {

			return false;

		}



	}



	public function GetProcessPlantData_byentry($entry_no)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'inprocess_plant`');



		$this->db->where(db_prefix() . 'inprocess_plant`.entry_no', $entry_no);

		return $this->db->get()->row();

	}



	public function GetProcessPlantDetail_byentry($entry_no)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'inprocess_plant_detail');

		$this->db->where(db_prefix() . 'inprocess_plant_detail.entry_no', $entry_no);

		return $this->db->get()->result_array();

	}



	public function update_Metal_Detector_Test_Data($data, $id)
	{



		if (isset($data['QC_detail'])) {

			$QC_detail = json_decode($data['QC_detail']);

			unset($data['QC_detail']);

			$es_detail = [];

			$header = [];



			$header[] = 'time';

			$header[] = 'mic_no';

			$header[] = 'metal_detector';

			$header[] = 'ferrous';

			$header[] = 'non_ferrous';

			$header[] = 's_s';

			$header[] = 'remark';

			foreach ($QC_detail as $key => $value) {



				if ($value[0] != '') {

					$es_detail[] = array_combine($header, $value);

				}

			}

		}

		$PlantID = $this->session->userdata('root_company');

		$FY = $this->session->userdata('finacial_year');



		$Transdate = date('Y-m-d H:i:s');



		$new_qc_no = get_option('next_metal_detector_test_number_for_cspl');

		$next_qc_no = "MDR" . $FY . $new_qc_no;



		if (count($es_detail) > 0) {

			$date = to_sql_date($data['TransDate']) . " " . date('H:i:s');



			if (!empty($id)) {

				$update_array = array(

					'TransDate' => $date,

					'UserID2' => $_SESSION['username'],

					'Lupdate' => date('Y-m-d H:i:s'),



				);



				$this->db->where('entry_no', $id);

				$this->db->update(db_prefix() . 'metal_detector_report`', $update_array);





				$i = 1;

				foreach ($es_detail as $value) {

					$data_array_result = array(

						'time' => $value['time'],

						'mic_no' => $value['mic_no'],

						'metal_detector' => $value['metal_detector'],

						'ferrous' => $value['ferrous'],

						'non_ferrous' => $value['non_ferrous'],

						's_s' => $value['s_s'],

						'remark' => $value['remark'],

						'TransDate' => $date,

						'UserID2' => $_SESSION['username'],

						'lupdate' => date('Y-m-d H:i:s'),

					);



					$this->db->where('entry_no', $id);

					$this->db->where('time', $value['time']);

					$this->db->update(db_prefix() . 'metal_detector_report_detail', $data_array_result);

				}

				return true;



			} else {

				$data_array = array(

					'entry_no ' => $next_qc_no,

					'TransDate' => $date,

					'UserID' => $_SESSION['username'],

					'TransDate2' => date('Y-m-d H:i:s'),

				);



				$this->db->insert(db_prefix() . 'metal_detector_report', $data_array);

				if ($this->db->affected_rows() > 0) {



					// Update next Inspection number in settings

					$next_number = $new_qc_no + 1;

					$this->db->where('name', 'next_metal_detector_test_number_for_cspl');

					$this->db->where('FY', $FY);

					$this->db->update(db_prefix() . 'options', ['value' => $next_number]);



					// Update Data In Detail Table	

					$i = 1;

					foreach ($es_detail as $value) {

						$data_array_result = array(

							'entry_no ' => $next_qc_no,

							'TransDate' => $date,

							'time' => $value['time'],

							'mic_no' => $value['mic_no'],

							'metal_detector' => $value['metal_detector'],

							'ferrous' => $value['ferrous'],

							'non_ferrous' => $value['non_ferrous'],

							's_s' => $value['s_s'],

							'remark' => $value['remark'],

							'TransDate2' => date('Y-m-d H:i:s'),

							'UserID' => $_SESSION['username'],

						);



						$this->db->insert(db_prefix() . 'metal_detector_report_detail', $data_array_result);

					}

					return true;

				} else {



					return false;

				}

			}

		} else {

			return false;

		}



	}



	public function GetMetalDetectorData_byentry($entry_no)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'metal_detector_report`');



		$this->db->where(db_prefix() . 'metal_detector_report`.entry_no', $entry_no);

		return $this->db->get()->row();

	}

	public function GetMetalDetectorDetail_byentry($entry_no)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];

		$this->db->select();

		$this->db->from(db_prefix() . 'metal_detector_report_detail');

		$this->db->where(db_prefix() . 'metal_detector_report_detail.entry_no', $entry_no);

		return $this->db->get()->result_array();

	}

	public function load_data_for_fg_test_entry($data)
	{

		$selected_company = $this->session->userdata('root_company');

		$from_date = to_sql_date($data["from_date"]);

		$to_date = to_sql_date($data["to_date"]);



		$sql1 = db_prefix() . 'fg_test_report.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59"';

		$sql1 .= '  ORDER BY entry_no DESC';



		$sql = 'SELECT *

			FROM ' . db_prefix() . 'fg_test_report

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}



	public function load_data_for_metal_detector_entry($data)
	{

		$selected_company = $this->session->userdata('root_company');

		$from_date = to_sql_date($data["from_date"]);

		$to_date = to_sql_date($data["to_date"]);



		$sql1 = db_prefix() . 'metal_detector_report.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59"';

		$sql1 .= '  ORDER BY entry_no DESC';



		$sql = 'SELECT *

			FROM ' . db_prefix() . 'metal_detector_report

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}

	public function load_data_for_process_plant_entry($data)
	{

		$selected_company = $this->session->userdata('root_company');

		$from_date = to_sql_date($data["from_date"]);

		$to_date = to_sql_date($data["to_date"]);



		$sql1 = db_prefix() . 'inprocess_plant.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59"';

		$sql1 .= '  ORDER BY entry_no DESC';



		$sql = 'SELECT *

			FROM ' . db_prefix() . 'inprocess_plant

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}



	public function GetBillsPayableBodyData($filterdata)
	{

		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$DueOn = !empty($filterdata["DueOn"]) ? $filterdata["DueOn"] : 'DESC';



		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		// Step 1: Get Purchases with clients

		$sql = 'SELECT pm.*, 

			c.company, 

			COALESCE(c.credit_limit,0) AS MaxDays

			FROM ' . db_prefix() . 'purchasemaster pm

			INNER JOIN tblclients c ON c.AccountID = pm.AccountID

			WHERE pm.cur_status="Completed" 

			AND pm.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59"

			AND c.SubActGroupID1 IN("100023")

			ORDER BY pm.AccountID DESC, pm.Transdate ' . $DueOn;



		$result = $this->db->query($sql)->result_array();



		if (empty($result))
			return [];



		// Collect PurchIDs & AccountIDs

		$purchIDs = array_column($result, 'PurchID');

		$accountIDs = array_column($result, 'AccountID');



		$inPurchIDs = implode('","', $purchIDs);

		$inAccountIDs = implode('","', $accountIDs);



		// Step 2: Aggregate ledger amounts in one query

		$ledgerSql = '

			SELECT BillNo,

			SUM(CASE WHEN TType="D" AND PassedFrom="PAYMENTS"   THEN Amount ELSE 0 END) AS PaidAmt,

			SUM(CASE WHEN TType="D" AND PassedFrom="PURCHASERTN" THEN Amount ELSE 0 END) AS PurchRtnAmt,

			SUM(CASE WHEN TType="D" AND PassedFrom="CDNOTE"      THEN Amount ELSE 0 END) AS DebitNoteAmt,

			SUM(CASE WHEN TType="D" AND PassedFrom="JOURNAL"     THEN Amount ELSE 0 END) AS JournalDebitAmt,

			SUM(CASE WHEN TType="C" AND PassedFrom="JOURNAL"     THEN Amount ELSE 0 END) AS JournalCreditAmt

			FROM tblaccountledger

			WHERE BillNo IN ("' . $inPurchIDs . '")

			GROUP BY BillNo

			';

		$ledgerData = $this->db->query($ledgerSql)->result_array();



		// Map ledger by PurchID

		$ledgerMap = [];

		foreach ($ledgerData as $row) {

			$ledgerMap[$row['BillNo']] = $row;

		}



		// Step 3: Fetch Vendor Discounts for all Accounts at once

		$discSql = 'SELECT * 

			FROM tblVendorWiseDiscPercentage

			WHERE AccountID IN ("' . $inAccountIDs . '")

			ORDER BY AccountID, Days DESC';

		$discData = $this->db->query($discSql)->result_array();



		// Map discounts per vendor

		$discMap = [];

		foreach ($discData as $row) {

			$discMap[$row['AccountID']][] = $row;

		}



		// Step 4: Merge results

		foreach ($result as &$each) {

			$pid = $each['PurchID'];

			$aid = $each['AccountID'];



			$led = isset($ledgerMap[$pid]) ? $ledgerMap[$pid] : [

				'PaidAmt' => 0,
				'PurchRtnAmt' => 0,
				'DebitNoteAmt' => 0,

				'JournalDebitAmt' => 0,
				'JournalCreditAmt' => 0

			];



			$each['PaidAmt'] = $led['PaidAmt'];

			$each['PurchRtnAmt'] = $led['PurchRtnAmt'];

			$each['DebitNoteAmt'] = $led['DebitNoteAmt'];

			$each['JournalAmt'] = $led['JournalDebitAmt'] - $led['JournalCreditAmt'];



			$each['DisDays'] = isset($discMap[$aid]) ? $discMap[$aid] : [];

		}



		return $result;

	}





	public function ItemMainGroups()
	{

		$id = array('1', '2', '3', '14');

		$this->db->select('*');

		$this->db->from(db_prefix() . 'items_main_groups');

		$this->db->where_in('id', $id);

		return $this->db->get()->result_array();

	}

	public function ItemSubGroups()
	{

		$id = array('1', '2');

		$id2 = array('1', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '32');

		$this->db->select('*');

		$this->db->from(db_prefix() . 'items_sub_group2');

		$this->db->where_in('main_DivisionID', $id);

		$this->db->where_in('sub_DivisionID1', $id2);

		return $this->db->get()->result_array();

	}

	public function get_charges_entry_detail_full($peid)
	{

		$selected_company = $this->session->userdata('root_company');

		$year = $_SESSION['finacial_year'];



		$this->db->select(db_prefix() . 'purchase_charges.*,tblclients.AccountID AS Account_name,tblclients.company,tblpurchase_charges.gst_per AS Gst,tblpurchase_charges.cgst AS cgst_amt,tblpurchase_charges.sgst AS sgst_amt,tblpurchase_charges.igst AS igst_amt,tblpurchase_charges.amount AS NetAmt');

		$this->db->from(db_prefix() . 'purchase_charges');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchase_charges.AccountID', 'left');

		$this->db->where(db_prefix() . 'purchase_charges.PONumber', $peid);

		$data = $this->db->get()->result_array();

		return $data;



	}



	public function GetSubgroup1Data($MainGroupId)
	{

		$this->db->select(db_prefix() . 'items_sub_groups.*');

		$this->db->where(db_prefix() . 'items_sub_groups.main_DivisionID', $MainGroupId);

		$this->db->order_by(db_prefix() . 'items_sub_groups.name', 'ASC');

		return $this->db->get('tblitemsSubGroup2')->result_array();

	}

	public function GetSubgroup2Data($SubGroup1)
	{

		$this->db->select(db_prefix() . 'items_sub_group2.*');

		$this->db->where(db_prefix() . 'items_sub_group2.sub_DivisionID1', $SubGroup1);

		$this->db->order_by(db_prefix() . 'items_sub_group2.name', 'ASC');

		return $this->db->get('tblitems_sub_group2')->result_array();

	}







	public function TodaysPurchaseStatus($data = "")
	{

		if (!empty($data)) {

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

		} else {

			$from_date = date('Y-m-d');

			$to_date = date('Y-m-d');

		}

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'purchaseordermaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'purchaseordermaster.FY = "' . $fy . '" AND ' . db_prefix() . 'purchaseordermaster.PlantID = "' . $selected_company . '"';



		$sql1 .= '  GROUP BY ' . db_prefix() . 'purchaseordermaster.cur_status ORDER BY tblpurchaseordermaster.Transdate ASC';



		$sql = 'SELECT ' . db_prefix() . 'purchaseordermaster.cur_status,COUNT(*) as count

			FROM ' . db_prefix() . 'purchaseordermaster 

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}



	public function TodaysPurchaseEntryStatus($data = "")
	{

		if (!empty($data)) {

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

		} else {

			$from_date = date('Y-m-d');

			$to_date = date('Y-m-d');

		}

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'purchasemaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'purchasemaster.FY = "' . $fy . '" AND ' . db_prefix() . 'purchasemaster.PlantID = "' . $selected_company . '"';



		$sql1 .= '  GROUP BY ' . db_prefix() . 'purchasemaster.cur_status ORDER BY tblpurchasemaster.Transdate ASC';



		$sql = 'SELECT ' . db_prefix() . 'purchasemaster.cur_status,COUNT(*) as count

			FROM ' . db_prefix() . 'purchasemaster 

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}

	public function TotalPurchaseSKU($data = "")
	{

		if (!empty($data)) {

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

		} else {

			$from_date = date('Y-m-01');

			$to_date = date('Y-m-d');

		}

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'history.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'history.FY = "' . $fy . '" AND ' . db_prefix() . 'history.PlantID = "' . $selected_company . '" AND tblhistory.TType="P" AND tblhistory.TType2="Purchase"';



		$sql1 .= '  GROUP BY ' . db_prefix() . 'history.ItemID ORDER BY tblhistory.TransDate ASC';



		$sql = 'SELECT ' . db_prefix() . 'items.MainGrpID,COUNT(*) as count

			FROM ' . db_prefix() . 'history

			

			LEFT JOIN tblitems ON UPPER(tblitems.ItemID) = UPPER(tblhistory.ItemID)

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}

	public function TotalPurchaseVendors($data = "")
	{

		if (!empty($data)) {

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

		} else {

			$from_date = date('Y-m-01');

			$to_date = date('Y-m-d');

		}

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'history.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'history.FY = "' . $fy . '" AND ' . db_prefix() . 'history.PlantID = "' . $selected_company . '" AND tblhistory.TType="P" AND tblhistory.TType2="Purchase"';



		$sql1 .= '  GROUP BY ' . db_prefix() . 'history.AccountID ORDER BY tblhistory.TransDate ASC';



		$sql = 'SELECT ' . db_prefix() . 'clients.SubActGroupID,COUNT(*) as count

			FROM ' . db_prefix() . 'history

			

			LEFT JOIN tblclients ON UPPER(tblclients.AccountID) = UPPER(tblhistory.AccountID)

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}

	public function TotalCompletedInvoices($data = "")
	{

		if (!empty($data)) {

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

		} else {

			$from_date = date('Y-m-01');

			$to_date = date('Y-m-d');

		}

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql = 'SELECT 

			COUNT(CASE WHEN DATE(Transdate) = CURDATE() THEN 1 END) AS today_invoice_count,

			SUM(CASE WHEN DATE(Transdate) = CURDATE() THEN Invamt ELSE 0 END) AS today_invoice_total,

			SUM(CASE WHEN DATE(Transdate) = CURDATE() THEN cgstamt+sgstamt+igstamt ELSE 0 END) AS today_invoice_gst,

			

			COUNT(CASE WHEN MONTH(Transdate) = MONTH(CURRENT_DATE()) 

			AND YEAR(Transdate) = YEAR(CURRENT_DATE()) THEN 1 END) AS monthly_invoice_count,

			SUM(CASE WHEN MONTH(Transdate) = MONTH(CURRENT_DATE()) 

			AND YEAR(Transdate) = YEAR(CURRENT_DATE()) THEN Invamt ELSE 0 END) AS monthly_invoice_total,

			SUM(CASE WHEN MONTH(Transdate) = MONTH(CURRENT_DATE()) 

			AND YEAR(Transdate) = YEAR(CURRENT_DATE()) THEN cgstamt+sgstamt+igstamt ELSE 0 END) AS monthly_invoice_gst

			FROM tblpurchasemaster WHERE cur_status = "Completed";

			';

		$result = $this->db->query($sql)->row();

		return $result;

	}





	public function TopRMLowestPurchaseSKU($data = "")
	{

		if (!empty($data)) {

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

		} else {

			$from_date = date('Y-m-01');

			$to_date = date('Y-m-d');

		}

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'history.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'history.FY = "' . $fy . '" AND ' . db_prefix() . 'history.PlantID = "' . $selected_company . '" AND tblhistory.TType="P" AND tblhistory.TType2="Purchase" AND tblitems.MainGrpID = "2" AND tblitems.SubGrpID2 IN ("17", "18", "69")';



		$sql1 .= '  GROUP BY ' . db_prefix() . 'items.SubGrpID2 ORDER BY tblhistory.BasicRate ASC LIMIT 3';



		$sql = 'SELECT ' . db_prefix() . 'items.description,' . db_prefix() . 'items.SubGrpID2,tblhistory.BasicRate,tblclients.company

			FROM ' . db_prefix() . 'history

			

			LEFT JOIN tblitems ON UPPER(tblitems.ItemID) = UPPER(tblhistory.ItemID)

			LEFT JOIN tblclients ON UPPER(tblclients.AccountID) = UPPER(tblhistory.AccountID)

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}

	public function TopPMLowestPurchaseSKU($data = "")
	{

		if (!empty($data)) {

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

		} else {

			$from_date = date('Y-m-01');

			$to_date = date('Y-m-d');

		}

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'history.TransDate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'history.FY = "' . $fy . '" AND ' . db_prefix() . 'history.PlantID = "' . $selected_company . '" AND tblhistory.TType="P" AND tblhistory.TType2="Purchase" AND tblitems.MainGrpID = "3"';



		$sql1 .= '  GROUP BY ' . db_prefix() . 'items.SubGrpID2 ORDER BY tblhistory.BasicRate ASC LIMIT 3';



		$sql = 'SELECT ' . db_prefix() . 'items.description,' . db_prefix() . 'items.SubGrpID2,tblhistory.BasicRate,tblclients.company

			FROM ' . db_prefix() . 'history

			

			LEFT JOIN tblitems ON UPPER(tblitems.ItemID) = UPPER(tblhistory.ItemID)

			LEFT JOIN tblclients ON UPPER(tblclients.AccountID) = UPPER(tblhistory.AccountID)

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}





	public function TopPartyByPurchAmt($data = "")
	{

		if (!empty($data)) {

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

		} else {

			$from_date = date('Y-m-01');

			$to_date = date('Y-m-d');

		}

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'purchasemaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'purchasemaster.FY = "' . $fy . '" AND ' . db_prefix() . 'purchasemaster.PlantID = "' . $selected_company . '"  AND tblclients.SubActGroupID IN ("1000186", "1000188")  AND ' . db_prefix() . 'purchasemaster.cur_status = "Completed"';



		$sql1 .= '  GROUP BY ' . db_prefix() . 'clients.SubActGroupID ORDER BY tblpurchasemaster.Invamt DESC Limit 2';



		$sql = 'SELECT ' . db_prefix() . 'clients.SubActGroupID,' . db_prefix() . 'purchasemaster.Invamt,tblclients.company

			FROM ' . db_prefix() . 'purchasemaster 

			LEFT JOIN tblclients ON UPPER(tblclients.AccountID) = UPPER(tblpurchasemaster.AccountID)

			WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}



	public function LoadQCStatusList($data = "")
	{

		if (!empty($data)) {

			$from_date = to_sql_date($data["from_date"]);

			$to_date = to_sql_date($data["to_date"]);

		} else {

			$from_date = date('Y-m-d');

			$to_date = date('Y-m-d');

		}

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$sql1 = '(' . db_prefix() . 'purchasemaster.Transdate BETWEEN "' . $from_date . ' 00:00:00" AND "' . $to_date . ' 23:59:59") AND ' . db_prefix() . 'purchasemaster.FY = "' . $fy . '" AND ' . db_prefix() . 'purchasemaster.PlantID = "' . $selected_company . '" ';



		$sql1 .= ' ORDER BY Transdate DESC';

		$sql = 'SELECT ' . db_prefix() . 'purchasemaster.*,  

			(SELECT GROUP_CONCAT(company SEPARATOR ",") FROM ' . db_prefix() . 'clients WHERE ' . db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasemaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . $selected_company . ') as AccountName

			FROM ' . db_prefix() . 'purchasemaster WHERE ' . $sql1;

		$result = $this->db->query($sql)->result_array();



		foreach ($result as &$each) {

			$each['QCStatus'] = $this->GetItemWiseQCStatusByEntryNo($each['PurchID']);

		}

		return $result;

	}



	public function GetDaywisePurchaseForthisMonth($filter = "")
	{

		// $month_input = $filter['month']; // Example: '2024-11'

		// $selected_year = date('Y', strtotime($month_input . "-01")); // Extract year

		// $selected_month = date('m', strtotime($month_input . "-01")); // Extract month

		// $date = $month_input.'-01';//your given date

		// $first_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", first day of this month");

		// $first_date = date("Y-m-d",$first_date_find);



		// $last_date_find = strtotime(date("Y-m-d", strtotime($date)) . ", last day of this month");

		// $last_date = date("Y-m-d",$last_date_find);



		// $Currentdate = date('Y-m-d');

		// if($last_date > $Currentdate){

		// $todate = $Currentdate;

		// }else{

		// $todate = $last_date;

		// }

		$from_date = to_sql_date($filter["from_date"]);

		$to_date = to_sql_date($filter["to_date"]);

		$ReportIn = $filter["ReportIn"];

		$to_date_new = date('Y-m-d', strtotime($to_date . ' +1 day'));

		$period = new DatePeriod(

			new DateTime($from_date),

			new DateInterval('P1D'),

			new DateTime($to_date_new)

		);

		$filter["from_date"] = $from_date;

		$filter["to_date"] = $to_date;



		$DayWisePurchase = $this->GetDayWisePurchaseReport($filter);



		$labels = [];

		$totals = [];

		if ($ReportIn == 'qty') {

			$LabelType = "Quantity";

		} else {

			$LabelType = "Amount";

		}

		//$types  = $this->get();

		// Get the current date

		$i = 1;

		foreach ($period as $key => $value) {

			$date = $value->format('d/m/Y');

			$date2 = $value->format('Y-m-d');

			$lable = substr($date, 0, 2) . "-" . date("M", strtotime($date2));

			array_push($labels, $lable);

			$DayPurchase = 0;

			foreach ($DayWisePurchase as $key1 => $value1) {

				if (substr($value1['TransDate2'], 0, 10) == $date2) {

					if ($ReportIn == 'qty') {

						$DayPurchase = $value1["QtySum"];

					} else {

						$DayPurchase = $value1["AmtSum"];

					}

				}

			}

			array_push($totals, $DayPurchase);

			$i++;

		}

		$chart = [

			'labels' => $labels,

			'datasets' => [

				[

					'label' => $LabelType,

					'backgroundColor' => 'rgba(37,155,35,0.2)',

					'borderColor' => '#84c529',

					'tension' => false,

					'borderWidth' => 1,

					'data' => $totals,

				],

			],

		];



		return $chart;

	}



	public function GetDayWisePurchaseReport($filterdata = "")
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = $filterdata["from_date"] . ' 00:00:00';

		$to_date = $filterdata["to_date"] . ' 23:59:59';

		$this->db->select('tblhistory.TransDate2,SUM(tblhistory.NetChallanAmt) AS AmtSum,SUM(tblhistory.BilledQty) AS QtySum,SUM(tblhistory.cgstamt) AS cgstamtSum,SUM(tblhistory.sgstamt) AS sgstamtSum,

			SUM(tblhistory.igstamt) AS igstamtSum');



		$this->db->join('tblitems', 'tblitems.ItemID = tblhistory.ItemID');

		if ($filterdata["SubGroup"]) {

			$this->db->where_in('tblitems.SubGrpID1', $filterdata["SubGroup"]);

		}

		if ($filterdata["Items"]) {

			$this->db->where_in('tblitems.ItemID', $filterdata["Items"]);

		}

		if ($filterdata["state"]) {

			$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID');

			$this->db->where_in('tblclients.state', $filterdata["state"]);

		}

		$this->db->where('tblhistory.PlantID', $selected_company);

		$this->db->where('tblhistory.FY', $fy);

		$this->db->where('tblhistory.BillID IS NOT NULL');

		$this->db->where('tblhistory.TType', "P");

		$this->db->where('tblhistory.TType2', "Purchase");



		$this->db->where("TransDate2 BETWEEN '$from_date' AND '$to_date'");

		$this->db->group_by('DATE(tblhistory.TransDate2)');

		$this->db->order_by('tblhistory.TransDate2', 'ASC');

		return $this->db->get('tblhistory')->result_array();

	}



	public function GetTopPurchaseItem($filterdata)
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



		if (!empty($filterdata["from_date"])) {

			$from_date = to_sql_date($filterdata["from_date"]);

			$to_date = to_sql_date($filterdata["to_date"]);

		} else {

			$from_date = date('Y-m-01');

			$to_date = date('Y-m-d');

		}

		$ItemCount = $filterdata["MaxCount"];

		$state = $filterdata["state"];

		$SubGroup = $filterdata["SubGroup"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$Items = $filterdata["Items"];

		$maingroupid = $filterdata["maingroupid"];

		$ReportIn = $filterdata["ReportIn"];



		$chart = [];

		if ($SubGroup) {

			$this->db->select(db_prefix() . 'history.ItemID,SUM(tblhistory.NetChallanAmt) AS AmtSum, SUM(BilledQty) as total_qty,' . db_prefix() . 'items.description as description_name');

		} else {

			$this->db->select(db_prefix() . 'items_sub_groups.id as ItemID,SUM(tblhistory.NetChallanAmt) AS AmtSum, SUM(BilledQty) as total_qty,' . db_prefix() . 'items_sub_groups.name as description_name');

		}

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID', 'INNER');



		if ($SubGroup) {



		} else {

			$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');

		}

		$this->db->where('tblhistory.PlantID', $selected_company);

		$this->db->where('tblhistory.FY', $fy);

		$this->db->where('tblhistory.TransDate >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.TType ', 'P');

		$this->db->where('tblhistory.TType2 ', 'Purchase');

		$this->db->where('tblhistory.BillID IS NOT NULL');

		if (!empty($state)) {

			$this->db->where('tblclients.state', $state);

		}



		if ($SubGroup) {

			$this->db->where_in('tblitems.SubGrpID1', $SubGroup);

			$this->db->group_by('tblhistory.ItemID');

		} else {

			$this->db->group_by('tblitems.SubGrpID1');

		}

		if ($SubGroup2) {

			$this->db->where_in('tblitems.SubGrpID2', $SubGroup2);

		}

		if ($Items) {

			$this->db->where_in('tblitems.ItemID', $Items);

		}

		if ($maingroupid) {

			$this->db->where_in('tblitems.MainGrpID', $maingroupid);

		}

		$this->db->order_by("total_qty", "DESC");



		$this->db->limit($ItemCount);

		$TopItem = $this->db->get(db_prefix() . 'history')->result_array();

		//return $TopItem;

		if ($ReportIn == 'qty') {

			$LabelType = "Quantity";

		} else {

			$LabelType = "Amount";

		}

		$itemIDs = [];

		$i = 0;

		foreach ($TopItem as $key => $value) {

			array_push($itemIDs, $value['ItemID']);

			if ($ReportIn == 'qty') {

				$TotalPurchase = (int) $value["total_qty"];

			} else {

				$TotalPurchase = (float) $value["AmtSum"];

			}

			array_push($chart, [

				'name' => $value['description_name'],

				'y' => $TotalPurchase,

				'z' => 100,

				'label' => $LabelType,

			]);

			$i++;

		}







		$data = [

			'ChartData' => $chart,

		];



		return $data;

	}





	public function get_main_groups()
	{

		//$selected_company = $this->session->userdata('root_company');

		$this->db->order_by('name', 'asc');

		//$this->db->where('PlantID', $selected_company);

		return $this->db->get(db_prefix() . 'items_main_groups')->result_array();

	}



	public function GetAllVendorList()
	{

		$selected_company = $this->session->userdata('root_company');

		$this->db->select('tblclients.ActSubGroupID2,tblclients.ActSubGroupID1,tblclients.ActMainGroupID, tblclients.AccountID, tblclients.company, tblclients.FavouringName, tblclients.PAN, tblclients.GSTIN, tblclients.OrganisationType, tblclients.GSTType, tblclients.IsActive', FALSE);

		$this->db->from('tblclients');  // Add this line - you were missing FROM

		$this->db->join('tblAccountSubGroup2', 'tblAccountSubGroup2.SubActGroupID = tblclients.ActSubGroupID2', 'LEFT');

		$this->db->where(db_prefix() . 'AccountSubGroup2.IsVendor', 'Y');


		$result = $this->db->get()->result_array();
		return $result;

	}



	// MainItemGroup Table Data

	public function get_MainItemGroup_data()
	{



		$this->db->select(db_prefix() . 'items_main_groups.*');

		$this->db->from(db_prefix() . 'items_main_groups');

		$this->db->order_by('id', 'ASC');

		return $this->db->get()->result_array();

	}

	public function GetAllCityList()
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');

		$sql = 'SELECT * FROM tblxx_citylist WHERE status = "1" Order By city_name ASC';

		$result = $this->db->query($sql)->result_array();

		return $result;

	}

	public function GetAllStationList()
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');

		$sql = 'SELECT * FROM tblStationMaster WHERE status = "1" Order By StationName ASC';

		$result = $this->db->query($sql)->result_array();

		return $result;

	}





	public function TotalOrders($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('COUNT(*) as Total');



		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchaseordermaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'purchaseordermaster.PlantID');

		$this->db->where('tblpurchaseordermaster.Transdate >=', $from_date . ' 00:00:00');

		$this->db->where('tblpurchaseordermaster.Transdate <=', $to_date . ' 23:59:59');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblclients.AccountID', $AccountID);

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$Transaction = $this->db->get('tblpurchaseordermaster')->row();

		// echo "<pre>";print_r($Transaction);die;



		return $Transaction->Total;

	}

	public function TotalEntryInvoice($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('tblpurchasemaster.cur_status,COUNT(*) as Total');



		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'purchasemaster.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'purchasemaster.PlantID');

		$this->db->where('tblpurchasemaster.Transdate >=', $from_date . ' 00:00:00');

		$this->db->where('tblpurchasemaster.Transdate <=', $to_date . ' 23:59:59');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblclients.AccountID', $AccountID);

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->group_by('tblpurchasemaster.cur_status');

		$Transaction = $this->db->get('tblpurchasemaster')->result_array();



		$Entry = 0;

		$Invoice = 0;

		foreach ($Transaction as $each) {

			if ($each['cur_status'] == 'Pending') {

				$Entry = $each['Total'];

			}

			if ($each['cur_status'] == 'Completed') {

				$Invoice = $each['Total'];

			}

		}

		$data = [

			'TotalPurchaseEntry' => $Entry,

			'TotalPurchaseInvoice' => $Invoice,

		];

		return $data;

	}



	public function TotalPurchaseAmt($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('ROUND(COALESCE(SUM(tblhistory.NetChallanAmt),0),2) as NetChallanAmt');



		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NOT NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->where('tblhistory.TType', 'P');

		$this->db->where('tblhistory.TType2', 'Purchase');

		$Transaction = $this->db->get('tblhistory')->row();

		// echo "<pre>";print_r($Transaction);die;



		return $Transaction->NetChallanAmt;

	}

	public function TotalPurchaseQuantity($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('ROUND(COALESCE(SUM(tblhistory.BilledQty),0),2) as BilledQty');



		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NOT NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->where('tblhistory.TType', 'P');

		$this->db->where('tblhistory.TType2', 'Purchase');

		$Transaction = $this->db->get('tblhistory')->row();

		// echo "<pre>";print_r($Transaction);die;



		return $Transaction->BilledQty;

	}



	public function AvgOrderValue($filterdata)
	{



		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('ROUND(AVG(tblhistory.NetChallanAmt),2) as AvgOrderAmt');



		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->where('tblhistory.TType', 'P');

		$this->db->where('tblhistory.TType2', 'Order');

		$Transaction = $this->db->get('tblhistory')->row();

		// echo "<pre>";print_r($Transaction);die;



		return $Transaction->AvgOrderAmt;

	}

	public function AvgOrderQty($filterdata)
	{



		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('ROUND(AVG(tblhistory.BilledQty),2) as AvgOrderQty');



		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->where('tblhistory.TType', 'P');

		$this->db->where('tblhistory.TType2', 'Order');

		$Transaction = $this->db->get('tblhistory')->row();

		// echo "<pre>";print_r($Transaction);die;



		return $Transaction->AvgOrderQty;

	}





	public function PurchaseReturnAmount($filterdata)
	{



		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('ROUND(COALESCE(SUM(tblhistory.NetChallanAmt),0),2) as ReturnAmt');



		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NOT NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->where('tblhistory.TType', 'N');

		$this->db->where('tblhistory.TType2', 'PurchaseReturn');

		$Transaction = $this->db->get('tblhistory')->row();

		// echo "<pre>";print_r($Transaction);die;



		return $Transaction->ReturnAmt;

	}



	public function PurchaseReturnQty($filterdata)
	{



		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('ROUND(COALESCE(SUM(tblhistory.BilledQty),0),2) as ReturnQty');



		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NOT NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->where('tblhistory.TType', 'N');

		$this->db->where('tblhistory.TType2', 'PurchaseReturn');

		$Transaction = $this->db->get('tblhistory')->row();

		// echo "<pre>";print_r($Transaction);die;



		return $Transaction->ReturnQty;

	}





	public function PurchaseGstAmount($filterdata)
	{



		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('ROUND(COALESCE(SUM(tblhistory.cgstamt+tblhistory.sgstamt+tblhistory.igstamt),0),2) as ReturnQty');



		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NOT NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->where('tblhistory.TType', 'P');

		$this->db->where('tblhistory.TType2', 'Purchase');

		$Transaction = $this->db->get('tblhistory')->row();

		// echo "<pre>";print_r($Transaction);die;



		return $Transaction->ReturnQty;

	}



	public function GetTopCustomer($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select(db_prefix() . 'history.AccountID, SUM(NetChallanAmt) as total_amt,' . db_prefix() . 'clients.company');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID', 'INNER');

		$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID', 'INNER');

		$this->db->where('tblhistory.PlantID', $selected_company);

		$this->db->where('tblhistory.FY', $fy);

		$this->db->where('tblhistory.TransDate >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.TType ', 'P');

		$this->db->where('tblhistory.TType2 ', 'Purchase');

		$this->db->where('tblhistory.TransID IS NOT NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->group_by('tblhistory.AccountID');

		$this->db->order_by("total_amt", "DESC");

		$this->db->limit('10');

		$Transaction = $this->db->get('tblhistory')->result_array();





		$chart = [];

		// print_r($TransTypes);die;

		foreach ($Transaction as $name => $value) {

			$chart[] = [

				'name' => $value['company'],

				'y' => (float) $value['total_amt'],

				'z' => 100,

				'label' => "Total"

			];

		}

		return $chart;

	}

	public function GetTopGroupItem($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];





		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->select('tblitemsSubGroup2.id as ItemID,tblitemsSubGroup2.name as ItemName,SUM(NetChallanAmt) as total_amt');

		} elseif (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->select('tblitems_sub_group2.id as ItemID,tblitems_sub_group2.name as ItemName,SUM(NetChallanAmt) as total_amt');

		} else {

			$this->db->select('tblitems.ItemID as ItemID,tblitems.description as ItemName,SUM(NetChallanAmt) as total_amt');

		}



		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID', 'INNER');

		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');

		}

		if (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->join(db_prefix() . 'items_sub_group2', db_prefix() . 'items_sub_group2.id = ' . db_prefix() . 'items.SubGrpID2');

		}

		$this->db->join(db_prefix() . 'clients', 'tblclients.AccountID = tblhistory.AccountID  AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID', 'INNER');

		$this->db->where('tblhistory.PlantID', $selected_company);

		$this->db->where('tblhistory.FY', $fy);

		$this->db->where('tblhistory.TransDate >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.TType ', 'P');

		$this->db->where('tblhistory.TType2 ', 'Purchase');

		$this->db->where('tblhistory.TransID IS NOT NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->group_by(db_prefix() . 'items.SubGrpID1');

		} elseif (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->group_by(db_prefix() . 'items.SubGrpID2');

		} else {

			$this->db->group_by(db_prefix() . 'items.ItemID');

		}

		$this->db->order_by("total_amt", "DESC");

		$this->db->limit('10');

		$Transaction = $this->db->get('tblhistory')->result_array();





		$chart = [];

		// print_r($TransTypes);die;

		foreach ($Transaction as $name => $value) {

			$chart[] = [

				'name' => $value['ItemName'],

				'y' => (float) $value['total_amt'],

				'z' => 100,

				'label' => "Total"

			];

		}

		return $chart;

	}



	public function AvgReturnOrder($filterdata)
	{



		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		$this->db->select('ROUND(AVG(tblhistory.NetChallanAmt),2) as AvgReturnAmt');



		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}

		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}

		$this->db->where('tblhistory.TType', 'N');

		$this->db->where('tblhistory.TType2', 'PurchaseReturn');

		$Transaction = $this->db->get('tblhistory')->row();

		// echo "<pre>";print_r($Transaction);die;



		return $Transaction->AvgReturnAmt;

	}



	public function GetMonthlyPurchase($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = (2000 + (int) $fy) . '-04-01';

		$to_date = date('Y-m-t');

		$start_year = 2000 + $fy;

		$end_year = $start_year + 1;

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];





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







		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->select('tblitemsSubGroup2.id as ItemID,tblitemsSubGroup2.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');

		} elseif (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->select('tblitems_sub_group2.id as ItemID,tblitems_sub_group2.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');

		} else {

			$this->db->select('tblitems.ItemID as ItemID,tblitems.description as ItemName,tblitems.case_qty AS CaseQty');

		}

		$this->db->from(db_prefix() . 'items');

		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');

		}

		if (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->join(db_prefix() . 'items_sub_group2', db_prefix() . 'items_sub_group2.id = ' . db_prefix() . 'items.SubGrpID2');

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

			$this->db->where('tblitems.MainGrpID', $MainItemGroup);

		}

		if (!empty($SubGroup1)) {

			$this->db->where('tblitems.SubGrpID1', $SubGroup1);

		}

		if (!empty($SubGroup2)) {

			$this->db->where('tblitems.SubGrpID2', $SubGroup2);

		}

		if (!empty($ItemID)) {

			$this->db->where('tblitems.ItemID', $ItemID);

		}

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

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





		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%b-%Y") as month,tblitemsSubGroup2.id as ItemID,tblitemsSubGroup2.name as ItemName,SUM(tblhistory.NetChallanAmt) as NetChallanAmt,tblhistory.TType,tblhistory.TType2');

		} elseif (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%b-%Y") as month,tblitems_sub_group2.id as ItemID,tblitems_sub_group2.name as ItemName,SUM(tblhistory.NetChallanAmt) as NetChallanAmt,tblhistory.TType,tblhistory.TType2');

		} else {

			$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%b-%Y") as month,tblitems.ItemID as ItemID,tblitems.description as ItemName,SUM(tblhistory.NetChallanAmt) as NetChallanAmt,tblhistory.TType,tblhistory.TType2');

		}

		$this->db->from('tblhistory');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');

		}

		if (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->join(db_prefix() . 'items_sub_group2', db_prefix() . 'items_sub_group2.id = ' . db_prefix() . 'items.SubGrpID2');

		}

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NOT NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}



		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}



		$this->db->where('tblhistory.TType', 'P');

		$this->db->where('tblhistory.TType2', 'Purchase');



		$this->db->group_by("YEAR(tblhistory.TransDate2), MONTH(tblhistory.TransDate2)");

		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->group_by(db_prefix() . 'items.SubGrpID1,tblhistory.TType,tblhistory.TType2');

		} elseif (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->group_by(db_prefix() . 'items.SubGrpID2,tblhistory.TType,tblhistory.TType2');

		} else {

			$this->db->group_by('tblhistory.ItemID,tblhistory.TType,tblhistory.TType2');

		}

		$Transaction = $this->db->get()->result_array();





		$chart = [];

		$groupData = [];



		foreach ($Group as $key1 => $value1) {



			$groupId = $value1['ItemID'];

			$groupName = $value1['ItemName'];





			// Filter transactions for this item

			$itemTrans = array_filter($Transaction, function ($tr) use ($groupId) {

				return trim(strtoupper($tr["ItemID"])) == trim(strtoupper($groupId));

			});



			// Re-index transactions month wise

			$monthlyTxn = [];

			foreach ($itemTrans as $tr) {

				$monthKey = $tr['month'];



				$NetAmt = 0;

				if ($tr["TType"] == "P" && $tr["TType2"] == "Purchase") {

					$NetAmt += $tr['NetChallanAmt'];

				}



				if (!isset($monthlyTxn[$monthKey])) {

					$monthlyTxn[$monthKey] = 0;

				}

				$monthlyTxn[$monthKey] = $NetAmt;

			}



			// Now cumulative month wise stock



			$dataPoints = [];



			foreach ($Months as $m) {

				$TotalAmt = 0;

				if (isset($monthlyTxn[$m])) {

					$TotalAmt = $monthlyTxn[$m];

				}

				$dataPoints[] = round($TotalAmt, 2);

			}

			if (array_sum($dataPoints) > 0) {

				$groupData[$groupId] = [

					'name' => $groupName,

					'data' => $dataPoints

				];

			}

		}



		$series = array_values($groupData);



		usort($series, function ($a, $b) {

			$lastA = end($a['data']);

			$lastB = end($b['data']);

			return $lastB <=> $lastA; // Desc order

		});



		// Take only top 20

		$series = array_slice($series, 0, 20);



		$ReturnData = [

			'Purchase' => $series,

			'Months' => $Months,

		];

		return $ReturnData;

	}



	public function GetDailyPurchase($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date_data = (2000 + (int) $fy) . '-04-01';

		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



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

			$this->db->select('tblitemsSubGroup2.id as ItemID,tblitemsSubGroup2.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');

		} elseif (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->select('tblitems_sub_group2.id as ItemID,tblitems_sub_group2.name as ItemName,AVG(tblitems.case_qty) AS CaseQty');

		} else {

			$this->db->select('tblitems.ItemID as ItemID,tblitems.description as ItemName,tblitems.case_qty AS CaseQty');

		}

		$this->db->from(db_prefix() . 'items');

		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');

		}

		if (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->join(db_prefix() . 'items_sub_group2', db_prefix() . 'items_sub_group2.id = ' . db_prefix() . 'items.SubGrpID2');

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

			$this->db->where('tblitems.MainGrpID', $MainItemGroup);

		}

		if (!empty($SubGroup1)) {

			$this->db->where('tblitems.SubGrpID1', $SubGroup1);

		}

		if (!empty($SubGroup2)) {

			$this->db->where('tblitems.SubGrpID2', $SubGroup2);

		}

		if (!empty($ItemID)) {

			$this->db->where('tblitems.ItemID', $ItemID);

		}

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

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



		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%d-%b-%Y") as day,tblitemsSubGroup2.id as ItemID,tblitemsSubGroup2.name as ItemName,SUM(tblhistory.NetChallanAmt) as NetChallanAmt,tblhistory.TType,tblhistory.TType2');

		} elseif (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%d-%b-%Y") as day,tblitems_sub_group2.id as ItemID,tblitems.description as ItemName,tblitems_sub_group2.name as ItemName,SUM(tblhistory.NetChallanAmt) as NetChallanAmt,tblhistory.TType,tblhistory.TType2');

		} else {

			$this->db->select('DATE_FORMAT(tblhistory.TransDate2, "%d-%b-%Y") as day,tblitems.ItemID as ItemID,SUM(tblhistory.NetChallanAmt) as NetChallanAmt,tblhistory.TType,tblhistory.TType2');

		}

		$this->db->from('tblhistory');

		$this->db->join(db_prefix() . 'items', db_prefix() . 'items.ItemID = ' . db_prefix() . 'history.ItemID AND ' . db_prefix() . 'items.PlantID = ' . db_prefix() . 'history.PlantID');

		$this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.AccountID = ' . db_prefix() . 'history.AccountID AND ' . db_prefix() . 'clients.PlantID = ' . db_prefix() . 'history.PlantID');

		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->join(db_prefix() . 'items_sub_groups', db_prefix() . 'items_sub_groups.id = ' . db_prefix() . 'items.SubGrpID1');

		}

		if (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->join(db_prefix() . 'items_sub_group2', db_prefix() . 'items_sub_group2.id = ' . db_prefix() . 'items.SubGrpID2');

		}

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.BillID IS NOT NULL');

		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}



		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}



		$this->db->where('tblhistory.TType', 'P');

		$this->db->where('tblhistory.TType2', 'Purchase');



		$this->db->group_by("DATE(tblhistory.TransDate2), tblhistory.ItemID, tblhistory.TType, tblhistory.TType2");



		$Transaction = $this->db->get()->result_array();

		// echo $this->db->last_query();die;

		// echo '<pre>';print_r($Transaction);die;

		/** ------------------------

			* Step 4: Build chart data

		* ------------------------ */

		$groupData = [];



		foreach ($Group as $g) {

			$groupId = $g['ItemID'];

			$groupName = $g['ItemName'];



			$itemTrans = array_filter($Transaction, function ($tr) use ($groupId) {

				return trim(strtoupper($tr["ItemID"])) == trim(strtoupper($groupId));

			});

			// Split: before from_date → add into OQty, else → day-wise

			$dailyTxn = [];

			foreach ($itemTrans as $tr) {

				$dayKey = $tr['day'];

				$transDate = DateTime::createFromFormat("d-M-Y", $dayKey)->format("Y-m-d");



				$Amount = 0;

				if ($tr["TType"] == "P" && $tr["TType2"] == "Purchase")
					$Amount = $tr['NetChallanAmt'];



				// Day-wise

				if (!isset($dailyTxn[$dayKey]))
					$dailyTxn[$dayKey] = 0;

				$dailyTxn[$dayKey] += $Amount;



			}





			// Cumulative calculation









			$dataPoints = [];

			foreach ($Days as $d) {

				$TotalAmt = 0;

				if (isset($dailyTxn[$d])) {

					$TotalAmt = $dailyTxn[$d];

				}



				$dataPoints[] = round($TotalAmt, 2);

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

			'Purchase' => $series,

			'Days' => $Days

		];

	}





	public function GetTopPurchaseRateByItemGroup($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		// ---------- 1. Get Purchases ----------

		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->select('tblitemsSubGroup2.id as ItemID,tblitemsSubGroup2.name as ItemName,SUM(NetChallanAmt) as purchase_amt');

		} elseif (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->select('tblitems_sub_group2.id as ItemID,tblitems_sub_group2.name as ItemName,SUM(NetChallanAmt) as purchase_amt');

		} else {

			$this->db->select('tblitems.ItemID as ItemID,tblitems.description as ItemName,SUM(NetChallanAmt) as purchase_amt');

		}



		$this->db->join('tblitems', 'tblitems.ItemID = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID', 'INNER');

		$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID', 'INNER');



		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->join('tblitemsSubGroup2', 'tblitemsSubGroup2.id = tblitems.SubGrpID1');

		}

		if (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->join('tblitems_sub_group2', 'tblitems_sub_group2.id = tblitems.SubGrpID2');

		}



		$this->db->where('tblhistory.PlantID', $selected_company);

		$this->db->where('tblhistory.FY', $fy);

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.TType', 'P');        // Purchase

		$this->db->where('tblhistory.TType2', 'Purchase');

		$this->db->where('tblhistory.BillID IS NOT NULL');



		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}



		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}





		if (!empty($MainItemGroup) && empty($SubGroup1)) {

			$this->db->group_by('tblitems.SubGrpID1');

		} elseif (!empty($SubGroup1) && empty($SubGroup2)) {

			$this->db->group_by('tblitems.SubGrpID2');

		} else {

			$this->db->group_by('tblitems.ItemID');

		}



		$PurchaseData = $this->db->get('tblhistory')->result_array();

		// echo "<pre>";print_r($PurchaseData);die;

		// ---------- 2. Calculate Total ----------

		$TotalPurchase = 0;

		foreach ($PurchaseData as $row) {

			$TotalPurchase += (float) $row['purchase_amt'];

		}



		// ---------- 3. Calculate % ----------

		$Report = [];

		foreach ($PurchaseData as $row) {

			$amt = (float) $row['purchase_amt'];

			$rate = ($TotalPurchase > 0) ? ($amt / $TotalPurchase) * 100 : 0;



			$Report[] = [

				'ItemName' => $row['ItemName'],

				'purchase' => $amt,

				'rate' => round($rate, 2)

			];

		}



		// ---------- 4. Sort by Purchase Rate ----------

		usort($Report, function ($a, $b) {

			return $b['rate'] <=> $a['rate'];

		});



		// ---------- 5. Limit Top 10 ----------

		$Report = array_slice($Report, 0, 10);



		// ---------- 6. Prepare Chart Data ----------

		$chart = [];

		foreach ($Report as $r) {

			if ($r['purchase'] > 0) {

				$chart[] = [

					'name' => $r['ItemName'] . " (" . $r['rate'] . "%)",

					'y' => $r['purchase'],

					'rate' => $r['rate']

				];

			}

		}



		return $chart;

	}

	public function GetTopPurchaseRateByVendor($filterdata)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');



		$from_date = to_sql_date($filterdata["from_date"]);

		$to_date = to_sql_date($filterdata["to_date"]);

		$TradeType = $filterdata["TradeType"];

		$AccountID = $filterdata["AccountID"];

		$MainItemGroup = $filterdata["MainItemGroup"];

		$SubGroup1 = $filterdata["SubGroup1"];

		$SubGroup2 = $filterdata["SubGroup2"];

		$ItemID = $filterdata["ItemID"];

		$ItemType = $filterdata["ItemType"];

		$Station = $filterdata["Station"];

		$City = $filterdata["City"];



		// ---------- 1. Get Purchases ----------



		$this->db->select('tblclients.AccountID,tblclients.company,SUM(NetChallanAmt) as purchase_amt');



		$this->db->join('tblitems', 'tblitems.ItemID = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID', 'INNER');

		$this->db->join('tblclients', 'tblclients.AccountID = tblhistory.AccountID AND tblclients.PlantID = tblhistory.PlantID', 'INNER');



		$this->db->where('tblhistory.PlantID', $selected_company);

		$this->db->where('tblhistory.FY', $fy);

		$this->db->where('tblhistory.TransDate2 >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate2 <=', $to_date . ' 23:59:59');

		$this->db->where('tblhistory.TType', 'P');        // Purchase

		$this->db->where('tblhistory.TType2', 'Purchase');

		$this->db->where('tblhistory.BillID IS NOT NULL');



		if (!empty($TradeType)) {

			$this->db->where('tblclients.Trade_Type', $TradeType);

		}

		if (!empty($AccountID)) {

			$this->db->where('tblhistory.AccountID', $AccountID);

		}

		if ($MainItemGroup !== "" && $MainItemGroup !== null) {

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

		if (!empty($ItemType)) {

			if ($ItemType == 'NonTaxable') {

				$this->db->where('tblitems.tax', '1');

			}

			if ($ItemType == 'Taxable') {

				$this->db->where('tblitems.tax !=', '1');

			}

		}



		if (!empty($Station)) {

			$this->db->where('tblclients.StationName', $Station);

		}

		if (!empty($City)) {

			$this->db->where('tblclients.city', $City);

		}



		$this->db->group_by('tblclients.AccountID');



		$PurchaseData = $this->db->get('tblhistory')->result_array();

		// echo "<pre>";print_r($PurchaseData);die;

		// ---------- 2. Calculate Total ----------

		$TotalPurchase = 0;

		foreach ($PurchaseData as $row) {

			$TotalPurchase += (float) $row['purchase_amt'];

		}



		// ---------- 3. Calculate % ----------

		$Report = [];

		foreach ($PurchaseData as $row) {

			$amt = (float) $row['purchase_amt'];

			$rate = ($TotalPurchase > 0) ? ($amt / $TotalPurchase) * 100 : 0;



			$Report[] = [

				'company' => $row['company'],

				'purchase' => $amt,

				'rate' => round($rate, 2)

			];

		}



		// ---------- 4. Sort by Purchase Rate ----------

		usort($Report, function ($a, $b) {

			return $b['rate'] <=> $a['rate'];

		});



		// ---------- 5. Limit Top 10 ----------

		$Report = array_slice($Report, 0, 10);



		// ---------- 6. Prepare Chart Data ----------

		$chart = [];

		foreach ($Report as $r) {

			if ($r['purchase'] > 0) {

				$chart[] = [

					'name' => $r['company'] . " (" . $r['rate'] . "%)",

					'y' => $r['purchase'],

					'rate' => $r['rate']

				];

			}

		}



		return $chart;

	}







	//===================== Get  Party List By Filter data =====================

	public function GetPartyListDateWise($data)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');

		$FromDate = to_sql_date($data["FromDate"]) . " 00:00:00";

		$ToDate = to_sql_date($data["ToDate"]) . " 23:59:59";



		$sql = 'SELECT tblpurchasemaster.AccountID,tblclients.company 

		FROM `tblpurchasemaster` 

		INNER JOIN tblclients ON tblclients.AccountID = tblpurchasemaster.AccountID 

		WHERE tblpurchasemaster.Transdate BETWEEN "' . $FromDate . '" AND "' . $ToDate . '" AND

		tblclients.PlantID = "' . $selected_company . '"';



		$sql .= ' GROUP BY tblpurchasemaster.AccountID ORDER BY tblclients.company';

		//echo $sql; die;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}



	//=======================  City List By Filter ====================

	public function GetPartyCityListByFilter($data)
	{

		$fy = $this->session->userdata('finacial_year');

		$selected_company = $this->session->userdata('root_company');

		$FromDate = to_sql_date($data["FromDate"]) . " 00:00:00";

		$ToDate = to_sql_date($data["ToDate"]) . " 23:59:59";

		$TradeType = $data["TradeType"];

		$sql = 'SELECT tblclients.city,tblxx_citylist.city_name 

		FROM `tblpurchasemaster` 

		INNER JOIN tblclients ON tblclients.AccountID = tblpurchasemaster.AccountID 

		INNER JOIN tblxx_citylist ON tblxx_citylist.id = tblclients.city 

		WHERE tblpurchasemaster.Transdate BETWEEN "' . $FromDate . '" AND "' . $ToDate . '" AND

		 tblclients.PlantID = "' . $selected_company . '"';

		if ($TradeType) {

			$sql .= ' AND tblclients.Trade_Type = "' . $TradeType . '" ';

		}

		$sql .= ' GROUP BY tblclients.city ORDER BY tblxx_citylist.city_name';

		//echo $sql; die;

		$result = $this->db->query($sql)->result_array();

		return $result;

	}



	//----------------------- Sub group 1 -----------------------------

	public function GetSubgroup1DateWise($data)
	{

		// print_r($data); die;

		$from_date = to_sql_date($data["FromDate"]) . " 00:00:00";

		$to_date = to_sql_date($data["ToDate"]) . " 23:59:59";

		$MainGroupId = $data["MainItemGroup"];



		$this->db->select('tblitemsSubGroup2.id,tblitemsSubGroup2.name');

		$this->db->join('tblitems', 'tblitems.ItemID = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID', 'INNER');

		$this->db->join('tblitemsSubGroup2', 'tblitemsSubGroup2.id = tblitems.SubGrpID1', 'INNER');

		$this->db->where(db_prefix() . 'items_sub_groups.main_DivisionID', $MainGroupId);

		$this->db->where('tblhistory.TType', "P");

		$this->db->where('tblhistory.TType2', "Purchase");

		$this->db->where('tblhistory.TransDate >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate <=', $to_date . ' 23:59:59');

		$this->db->group_by(db_prefix() . 'items_sub_groups.id');

		$this->db->order_by(db_prefix() . 'items_sub_groups.name', 'ASC');

		return $this->db->get('tblhistory')->result_array();

	}





	public function GetSubgroup2DateWise($data)
	{

		$from_date = to_sql_date($data["FromDate"]) . " 00:00:00";

		$to_date = to_sql_date($data["ToDate"]) . " 23:59:59";

		$SubGroup1 = $data["SubGroup1"];



		$this->db->select('tblitems_sub_group2.id,tblitems_sub_group2.name');

		$this->db->join('tblitems', 'tblitems.ItemID = tblhistory.ItemID AND tblitems.PlantID = tblhistory.PlantID', 'INNER');

		$this->db->join('tblitems_sub_group2', 'tblitems_sub_group2.sub_DivisionID1 = tblitems.SubGrpID1', 'INNER');



		$this->db->where(db_prefix() . 'items_sub_group2.sub_DivisionID1', $SubGroup1);

		$this->db->where('tblhistory.TType', "P");

		$this->db->where('tblhistory.TType2', "Purchase");

		$this->db->where('tblhistory.TransDate >=', $from_date . ' 00:00:00');

		$this->db->where('tblhistory.TransDate <=', $to_date . ' 23:59:59');

		$this->db->group_by(db_prefix() . 'items_sub_group2.id');

		$this->db->order_by(db_prefix() . 'items_sub_group2.name', 'ASC');

		return $this->db->get('tblhistory')->result_array();

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

	/**
	 * Get freight terms
	 * @return array
	 */
	public function get_freight_terms()
	{
		$this->db->select([db_prefix() . 'FreightTerms.*']);
		$this->db->where(db_prefix() . 'FreightTerms.IsActive', 'Y');
		$this->db->order_by(db_prefix() . 'FreightTerms.Id', 'ASC');
		return $this->db->get('tblFreightTerms')->result_array();
	}

	/**
	 * Get priority master
	 * @return array
	 */
	public function get_priority()
	{
		$this->db->select([db_prefix() . 'PriorityMaster.*']);
		$this->db->where(db_prefix() . 'PriorityMaster.IsActive', 'Y');
		$this->db->order_by(db_prefix() . 'PriorityMaster.Id', 'ASC');
		return $this->db->get('tblPriorityMaster')->result_array();
	}

	/**
	 * Get territory
	 * @return array
	 */
	public function get_territory()
	{
		$this->db->select([db_prefix() . 'Territory.*']);
		$this->db->where(db_prefix() . 'Territory.IsActive', 'Y');
		$this->db->order_by(db_prefix() . 'Territory.Id', 'ASC');
		return $this->db->get('tblTerritory')->result_array();
	}

	/**
	 * Get broker list
	 * @return array
	 */
	public function get_broker()
	{
		$this->db->select([db_prefix() . 'AccountSubGroup2.*']);
		$this->db->where(db_prefix() . 'AccountSubGroup2.IsBroker', 'Y');
		$this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');
		return $this->db->get('tblAccountSubGroup2')->result_array();
	}


		public function get_broker_name()
	{
		$this->db->select([db_prefix() . 'clients.*']);
		// $this->db->where(db_prefix() . 'AccountSubGroup2.IsBroker', 'Y');
		 $this->db->join( db_prefix() . 'AccountSubGroup2 a','a.SubActGroupID = tblclients.ActSubGroupID2','left');
		 $this->db->where('a.IsBroker', 'Y');
		$this->db->order_by(db_prefix() . 'clients.company', 'ASC');
		return $this->db->get('tblclients')->result_array();
	}


	/**
	 * Get position master
	 * @return array
	 */
	public function get_position()
	{
		$this->db->select(db_prefix() . 'hr_job_position.*');
		$this->db->from(db_prefix() . 'hr_job_position');
		$this->db->order_by(db_prefix() . 'hr_job_position.position_id', 'ASC');
		return $this->db->get()->result_array();
	}
	public function get_country()
	{
		$this->db->select(db_prefix() . 'countries.*');
		$this->db->from(db_prefix() . 'countries');
		$this->db->order_by(db_prefix() . 'countries.country_id', 'ASC');
		return $this->db->get()->result_array();
	}


	/**
	 * Get root company
	 * @return array
	 */
	public function get_rootcompany()
	{
		return $this->db->get(db_prefix() . 'rootcompany')->result_array();
	}

	/**
	 * Get vendor categories (IsVendor='Y')
	 * @return array
	 */
	public function get_vendor_categories()
	{
		$this->db->select([db_prefix() . 'AccountSubGroup2.*']);
		$this->db->where(db_prefix() . 'AccountSubGroup2.IsVendor', 'Y');
		$this->db->order_by(db_prefix() . 'AccountSubGroup2.SubActGroupName', 'ASC');
		return $this->db->get('tblAccountSubGroup2')->result_array();
	}


	/**
	 * Add new vendor
	 * @param array $data Form data from AddEditVendor view
	 * @return int Vendor ID on success, false on failure
	 */
	public function add_vendor($data)
	{
		$selected_company = $this->session->userdata('root_company');

		$vendor_data = array(
			'company' => isset($data['AccountName']) ? $data['AccountName'] : '',
			'FavouringName' => isset($data['favouring_name']) ? $data['favouring_name'] : '',
			'SubActGroupID1' => isset($data['vendor_type']) ? $data['vendor_type'] : '',
			'MobileNo' => isset($data['phonenumber']) ? $data['phonenumber'] : '',
			'Email' => isset($data['email']) ? $data['email'] : '',
			'GSTIN' => isset($data['vat']) ? strtoupper($data['vat']) : '',
			'Pan' => isset($data['pan']) ? strtoupper($data['pan']) : '',
			'billing_state' => isset($data['state']) ? $data['state'] : '',
			'billing_city' => isset($data['city']) ? $data['city'] : '',
			'billing_address' => isset($data['address']) ? $data['address'] : '',
			'billing_zip' => isset($data['zip']) ? $data['zip'] : '',
			'GSTType' => isset($data['gst_type']) ? $data['gst_type'] : '',
			'OrganisationType' => isset($data['organisation_type']) ? $data['organisation_type'] : '',
			'PlantID' => $selected_company,
			'CreatedDate' => date('Y-m-d H:i:s'),
		);

		// Debug logging
		log_message('debug', 'add_vendor - Data: ' . json_encode($vendor_data));

		$this->db->insert(db_prefix() . 'pur_vendor', $vendor_data);
		$vendor_id = $this->db->insert_id();

		// Log insert result
		log_message('debug', 'add_vendor - Result: ' . $vendor_id . ', Last DB Error: ' . $this->db->_error_message());

		return $vendor_id > 0 ? $vendor_id : false;
	}

	/**
	 * Update existing vendor
	 * @param array $data Form data from AddEditVendor view
	 * @param int $vendor_id Vendor ID to update
	 * @return bool Success status
	 */
	public function update_vendor($data, $vendor_id)
	{
		$selected_company = $this->session->userdata('root_company');

		$vendor_data = array(
			'company' => isset($data['AccountName']) ? $data['AccountName'] : '',
			'FavouringName' => isset($data['favouring_name']) ? $data['favouring_name'] : '',
			'SubActGroupID1' => isset($data['vendor_type']) ? $data['vendor_type'] : '',
			'MobileNo' => isset($data['phonenumber']) ? $data['phonenumber'] : '',
			'Email' => isset($data['email']) ? $data['email'] : '',
			'GSTIN' => isset($data['vat']) ? strtoupper($data['vat']) : '',
			'Pan' => isset($data['pan']) ? strtoupper($data['pan']) : '',
			'billing_state' => isset($data['state']) ? $data['state'] : '',
			'billing_city' => isset($data['city']) ? $data['city'] : '',
			'billing_address' => isset($data['address']) ? $data['address'] : '',
			'billing_zip' => isset($data['zip']) ? $data['zip'] : '',
			'GSTType' => isset($data['gst_type']) ? $data['gst_type'] : '',
			'OrganisationType' => isset($data['organisation_type']) ? $data['organisation_type'] : '',
			'UpdatedDate' => date('Y-m-d H:i:s'),
		);

		// Debug logging
		log_message('debug', 'update_vendor - Vendor ID: ' . $vendor_id . ', Data: ' . json_encode($vendor_data));

		$this->db->where('userid', $vendor_id);
		$this->db->where('PlantID', $selected_company);
		$this->db->update(db_prefix() . 'pur_vendor', $vendor_data);

		$affected = $this->db->affected_rows();
		// Log update result
		log_message('debug', 'update_vendor - Affected rows: ' . $affected . ', Last DB Error: ' . $this->db->_error_message());

		return $affected > 0;
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
	 * Get Next Vendor Code based on ActSubGroupID2 (Vendor Category)
	 * Counts existing vendors in this category and returns count + 1
	 * 
	 * @param string $ActSubGroupID2 - The vendor category ID
	 * @return array - Contains next_code, count, category_code, and category_name
	 */
	public function GetNextVendorCode($ActSubGroupID2)
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

	/**
	 * Get vendor name (IsVendor='Y')
	 * @return array
	 */
	public function get_vendor_name()
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

	public function get_purchase_order_data()
	{
		$this->db->select('*');
		$this->db->from('tblPurchaseOrderMaster');
		$this->db->order_by('id', 'ASC');

		return $this->db->get()->result_array();
	}

	public function get_order_history($order_id = null)
	{
		$this->db->select('*');
		$this->db->from(db_prefix() . 'history');
		if ($order_id !== null) {
			$this->db->where(db_prefix() . 'history.OrderID', $order_id);
		}
		$this->db->order_by('Ordinalno', 'ASC');
		$query = $this->db->get();
		return $query->result_array();
	}
	public function getDropdown($table, $fields, $where = null, $order = null, $orderBy = 'ASC')
	{
		$this->db->select($fields);
		$this->db->from(db_prefix() . $table);
		if ($where != null) {
			$this->db->where($where);
		}
		if ($order != null) {
			$this->db->order_by($order, $orderBy);
		}
		return $this->db->get()->result_array();
	}


	public function get_purchase_location()
	{
		$selected_company = $this->session->userdata('root_company');

		$this->db->select([db_prefix() . 'PlantLocationDetails.*']);
		$this->db->where(db_prefix() . 'PlantLocationDetails.PlantID', $selected_company);
		return $this->db->get('tblPlantLocationDetails')->result_array();
	}


	public function get_purchase_orderdata($PurchID)
	{
		$selected_company = $this->session->userdata('root_company');

		$this->db->select([db_prefix() . 'PurchaseOrderMaster.*']);
		$this->db->where(db_prefix() . 'PurchaseOrderMaster.PurchID', $PurchID);
		return $this->db->get('tblPurchaseOrderMaster')->result_array();
	}


	public function GetPurchaseOrderDetailsForPdf($PurchID)
	{
		$selected_company = $this->session->userdata('root_company');

		$this->db->select(
			db_prefix() . 'PurchaseOrderMaster.*, ' .
			db_prefix() . 'clients.company, ' .
			db_prefix() . 'clients.billing_address, ' .
			db_prefix() . 'clients.billing_city, ' .
			db_prefix() . 'clients.billing_state, ' .
			db_prefix() . 'clients.GSTIN, ' .
			db_prefix() . 'xx_statelist.state_name, ' .
			db_prefix() . 'ItemTypeMaster.ItemTypeName, ' .
			db_prefix() . 'ItemCategoryMaster.CategoryName, ' .
			db_prefix() . 'clientwiseshippingdata.ShippingCity, ' .
			'delivery_city.city_name, ' .
			db_prefix() . 'FreightTerms.FreightTerms, ' .
			db_prefix() . 'PlantLocationDetails.LocationName , ' .
			db_prefix() . 'PurchQuotationMaster.TransDate as QuotationDate, ' .
			'shipping_citys.city_name as ShippingCityName'
		);

		$this->db->join(
			db_prefix() . 'clients',
			db_prefix() . 'clients.AccountID = ' . db_prefix() . 'PurchaseOrderMaster.AccountID',
			'left'
		);

		$this->db->join(
			db_prefix() . 'xx_statelist',
			db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.billing_state',
			'left'
		);

		$this->db->join(
			db_prefix() . 'ItemTypeMaster',
			db_prefix() . 'ItemTypeMaster.Id = ' . db_prefix() . 'PurchaseOrderMaster.ItemType',
			'left'
		);

		$this->db->join(
			db_prefix() . 'ItemCategoryMaster',
			db_prefix() . 'ItemCategoryMaster.Id = ' . db_prefix() . 'PurchaseOrderMaster.ItemCategory',
			'left'
		);

		$this->db->join(
			db_prefix() . 'clientwiseshippingdata',
			db_prefix() . 'clientwiseshippingdata.id = ' . db_prefix() . 'PurchaseOrderMaster.DeliveryLocation',
			'left'
		);

		// Aliased to avoid conflict with the second xx_citylist join
		$this->db->join(
			db_prefix() . 'PlantLocationDetails',
			db_prefix() . 'PlantLocationDetails.id = ' . db_prefix() . 'PurchaseOrderMaster.PurchaseLocation',
			'left'
		)->join(db_prefix() . 'xx_citylist as delivery_city', 'delivery_city.id = delivery_city.Id', 'left');

		$this->db->join(
			db_prefix() . 'FreightTerms',
			db_prefix() . 'PurchaseOrderMaster.FreightTerms = ' . db_prefix() . 'FreightTerms.Id',
			'left'
		);

		$this->db->join(
			db_prefix() . 'PurchQuotationMaster',
			db_prefix() . 'PurchQuotationMaster.QuotatioonID = ' . db_prefix() . 'PurchaseOrderMaster.QuatationID',
			'left'
		);

		// Aliased for ShippingCity
		$this->db->join(
			db_prefix() . 'xx_citylist as shipping_citys',
			'shipping_citys.id = ' . db_prefix() . 'clientwiseshippingdata.ShippingCity',
			'left'
		);

		$this->db->where(db_prefix() . 'PurchaseOrderMaster.PurchID', $PurchID);

		return $this->db->get(db_prefix() . 'PurchaseOrderMaster')->row();
	}

	public function get_order_data($PurchID)
	{
		$selected_company = $this->session->userdata('root_company');

		$this->db->select([db_prefix() . 'history.*', 'tblitems.ItemName']);
		$this->db->join('tblitems', 'tblitems.ItemID = ' . db_prefix() . 'history.ItemID', 'left');
		$this->db->where(db_prefix() . 'history.OrderID', $PurchID);
		return $this->db->get(db_prefix() . 'history')->result_array();
	}



	public function getNextOrderNoByCategory($category_id)
	{
		$PlantID = $this->session->userdata('root_company');
		$FY = $this->session->userdata('finacial_year');

		if (!$category_id)
			return '';


		$this->db->where('ItemCategory', $category_id); // tblPurchaseOrderMaster madhye ItemCategory filter
		$count = $this->db->count_all_results(db_prefix() . 'PurchaseOrderMaster');

		$next_no = $count + 1;
		return str_pad($next_no, 5, '0', STR_PAD_LEFT);
	}
	public function getNextOrderNoByCategoryprefix($category_id)
	{
		$this->db->select('Prefix');
		$this->db->from('tblItemCategoryMaster');
		$this->db->where('id', $category_id);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			return $query->row()->Prefix;
		} else {
			return false;
		}
	}


	public function GetPurchaseOrderDetails($id)
	{
		$this->db->select('po.*, c.company');
		$this->db->from(db_prefix() . 'PurchaseOrderMaster po');
		$this->db->join(db_prefix() . 'clients c', 'c.AccountID = po.AccountID', 'left');
		$this->db->where('po.id', $id);
		$master = $this->db->get()->row_array();

		if (!$master) {
			return [];
		}

		$this->db->from(db_prefix() . 'history');
		$this->db->where('OrderID', $master['PurchID']);
		$history = $this->db->get()->result_array();
		$master['history'] = $history;

		return $master;
	}


	public function GetQuotationMaster($AccountID)
	{
		$this->db->select('QuotatioonID');

		$this->db->from('tblPurchQuotationMaster');
		$this->db->where('AccountID', $AccountID);
		$this->db->where('Status !=', 6);

		return $this->db->get()->result_array();
	}

	public function GetQuotationMasterdate($QuotationID)
	{
		$this->db->select('TransDate');
		$this->db->from('tblPurchQuotationMaster');
		$this->db->where('QuotatioonID', $QuotationID);

		return $this->db->get()->result_array();
	}


	public function get_item_category_list()
	{
		$this->db->select('*');
		$this->db->from('tblItemCategoryMaster');
		// $this->db->where('QuotatioonID', $QuotationID);

		return $this->db->get()->result_array();
	}


	public function GetquotationDetails($AccountID)
	{
		$this->db->select(
			'tblPurchaseOrderMaster.PurchID'
		);

		$this->db->from('tblPurchaseOrderMaster');
		$this->db->where('tblPurchaseOrderMaster.AccountID', $AccountID);

		return $this->db->get()->result_array();
	}
public function getCurrency($AccountID)
{
    $this->db->select('tblcurrencies.name as currency_name');

    $this->db->from('tblclients');
    $this->db->join('tblcurrencies', 'tblclients.default_currency = tblcurrencies.id', 'left');
    $this->db->where('tblclients.AccountID', $AccountID);

    return $this->db->get()->row_array();
}


public function GetvandocDetails($purchase_order_no)
{
    $this->db->select('
        tblPurchaseOrderMaster.VendorDocNo,
        tblPurchaseOrderMaster.QuatationID,
        tblPurchaseOrderMaster.VendorDocDate as TransDate,
        tblFreightTerms.FreightTerms,
        tblclients.company AS BrokerName,
        tblPurchaseOrderMaster.PaymentTerms,
        tblPurchaseOrderMaster.NetAmt,
        tblItemCategoryMaster.CategoryName,
        tblItemTypeMaster.ItemTypeName,
		tblPurchQuotationMaster.TransDate as qutotationdate
    ');
    $this->db->from('tblPurchaseOrderMaster');
    $this->db->join('tblFreightTerms', 'tblFreightTerms.Id = tblPurchaseOrderMaster.FreightTerms', 'left');
    $this->db->join('tblclients', 'tblclients.AccountID = tblPurchaseOrderMaster.BrokerID', 'left');
    $this->db->join('tblItemCategoryMaster', 'tblItemCategoryMaster.id = tblPurchaseOrderMaster.ItemCategory', 'left');
    $this->db->join('tblItemTypeMaster', 'tblItemTypeMaster.id = tblPurchaseOrderMaster.ItemType', 'left');
    $this->db->join('tblPurchQuotationMaster', 'tblPurchQuotationMaster.QuotatioonID = tblPurchaseOrderMaster.QuatationID', 'left');
    $this->db->where('tblPurchaseOrderMaster.PurchID', $purchase_order_no);

    return $this->db->get()->result_array();
}

public function getListByFilter($data, $limit, $offset)
{
    $state    = $data['state']    ?? '';
    $IsActive = $data['IsActive'] ?? 'Y';

    // SELECT must come before count
    $this->db->select(db_prefix() . 'clients.*, ' . db_prefix() . 'clients.company as customer_name,tblxx_statelist.state_name as state');
    $this->db->from(db_prefix() . 'clients');

    // JOIN must come before count
    $this->db->join(
        db_prefix() . 'AccountSubGroup2',
        db_prefix() . 'AccountSubGroup2.SubActGroupID = ' . db_prefix() . 'clients.ActSubGroupID2',
        'LEFT'
    );

	$this->db->join(
        db_prefix() . 'xx_statelist',
        db_prefix() . 'xx_statelist.short_name = ' . db_prefix() . 'clients.billing_state',
        'LEFT'
    );
    // WHERE conditions must come before count
    $this->db->where(db_prefix() . 'AccountSubGroup2.IsVendor', 'Y');

    if ($state    != '') $this->db->where(db_prefix() . 'xx_statelist.state_name', $state);
    if ($IsActive != '') $this->db->where(db_prefix() . 'clients.IsActive', $IsActive);

    // Count AFTER all conditions are set, FALSE = don't reset query
    $total = $this->db->count_all_results('', FALSE);

    // Now apply order + pagination
    $this->db->limit($limit, $offset);

    $rows = $this->db->get()->result_array();

    return [
        'total' => $total,
        'rows'  => $rows
    ];
}

}