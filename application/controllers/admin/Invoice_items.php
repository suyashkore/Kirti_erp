<?php



defined('BASEPATH') or exit('No direct script access allowed');



class Invoice_items extends AdminController

{

    private $not_importable_fields = ['id'];



    public function __construct()

    {

        parent::__construct();

        $this->load->model('invoice_items_model');
    }



    /* List all available items */

    public function index()

    {

        if (staff_cant('view', 'items')) {

            access_denied('Invoice Items');
        }



        $this->load->model('taxes_model');

        $data['taxes']        = $this->taxes_model->get();

        $data['items_groups'] = $this->invoice_items_model->get_groups();



        $this->load->model('currencies_model');

        $data['currencies'] = $this->currencies_model->get();



        $data['base_currency'] = $this->currencies_model->get_base_currency();



        $data['title'] = _l('invoice_items');

        $data['item_main_groups'] = $this->invoice_items_model->get_item_main_groups();

        $data['item_sub_groups'] = $this->invoice_items_model->get_sub_groups();

        $data['vendors_data'] = $this->invoice_items_model->GetVendorList();

        $data['hsn_data'] = $this->invoice_items_model->get_data_table();

        $data['units'] = $this->invoice_items_model->get_data_table_unit();

        $this->load->model('taxes_model');

        $data['taxes'] = $this->taxes_model->get();

        $data['MainItemGroup'] = $this->invoice_items_model->get_MainItemGroup_data();

        $this->load->view('admin/invoice_items/manage', $data);
    }
    public function add_item()
    {
        $this->load->model('taxes_model');

        $data['taxes']        = $this->taxes_model->get();

        $data['items_groups'] = $this->invoice_items_model->get_groups();



        $this->load->model('currencies_model');

        $data['currencies'] = $this->currencies_model->get();



        $data['base_currency'] = $this->currencies_model->get_base_currency();



        $data['title'] = _l('invoice_items');

        $data['item_main_groups'] = $this->invoice_items_model->get_item_main_groups();

        $data['item_sub_groups'] = $this->invoice_items_model->get_sub_groups();

        $data['vendors_data'] = $this->invoice_items_model->GetVendorList();

        $data['hsn_data'] = $this->invoice_items_model->get_data_table();

        $data['units'] = $this->invoice_items_model->get_data_table_unit();

        $this->load->model('taxes_model');

        $data['taxes'] = $this->taxes_model->get();

        $data['MainItemGroup'] = $this->invoice_items_model->get_MainItemGroup_data();
        $this->load->view('admin/invoice_items/item', $data);
    }



    public function table()

    {

        if (staff_cant('view', 'items')) {

            ajax_access_denied();
        }

        $this->app->get_table_data('invoice_items');
    }



    /* Edit or update items / ajax request /*/

    public function manage()

    {

        if (staff_can('view',  'items')) {

            if ($this->input->post()) {

                $data = $this->input->post();

                if ($data['itemid'] == '') {

                    if (staff_cant('create', 'items')) {

                        header('HTTP/1.0 400 Bad error');

                        echo _l('access_denied');

                        die;
                    }

                    $id      = $this->invoice_items_model->add($data);

                    $success = false;

                    $message = '';

                    if ($id) {

                        $success = true;

                        $message = _l('added_successfully', _l('sales_item'));
                    }

                    echo json_encode([

                        'success' => $success,

                        'message' => $message,

                        'item'    => $this->invoice_items_model->get($id),

                    ]);
                } else {

                    if (staff_cant('edit', 'items')) {

                        header('HTTP/1.0 400 Bad error');

                        echo _l('access_denied');

                        die;
                    }

                    $success = $this->invoice_items_model->edit($data);

                    $message = '';

                    if ($success) {

                        $message = _l('updated_successfully', _l('sales_item'));
                    }

                    echo json_encode([

                        'success' => $success,

                        'message' => $message,

                    ]);
                }
            }
        }
    }



    public function import()

    {

        if (staff_cant('create', 'items')) {

            access_denied('Items Import');
        }



        $this->load->library('import/import_items', [], 'import');



        $this->import->setDatabaseFields($this->db->list_fields(db_prefix() . 'items'))

            ->setCustomFields(get_custom_fields('items'));



        if ($this->input->post('download_sample') === 'true') {

            $this->import->downloadSample();
        }



        if (

            $this->input->post()

            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != ''

        ) {

            $this->import->setSimulation($this->input->post('simulate'))

                ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])

                ->setFilename($_FILES['file_csv']['name'])

                ->perform();



            $data['total_rows_post'] = $this->import->totalRows();



            if (!$this->import->isSimulation()) {

                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }



        $data['title'] = _l('import');

        $this->load->view('admin/invoice_items/import', $data);
    }



    public function add_group()

    {

        if ($this->input->post() && staff_can('create',  'items')) {

            $this->invoice_items_model->add_group($this->input->post());

            set_alert('success', _l('added_successfully', _l('item_group')));
        }
    }



    public function update_group($id)

    {

        if ($this->input->post() && staff_can('edit',  'items')) {

            $this->invoice_items_model->edit_group($this->input->post(), $id);

            set_alert('success', _l('updated_successfully', _l('item_group')));
        }
    }



    public function delete_group($id)

    {

        if (staff_can('delete',  'items')) {

            if ($this->invoice_items_model->delete_group($id)) {

                set_alert('success', _l('deleted', _l('item_group')));
            }
        }

        redirect(admin_url('invoice_items?groups_modal=true'));
    }



    /* Delete item*/

    public function delete($id)

    {

        if (staff_cant('delete', 'items')) {

            access_denied('Invoice Items');
        }



        if (!$id) {

            redirect(admin_url('invoice_items'));
        }



        $response = $this->invoice_items_model->delete($id);

        if (is_array($response) && isset($response['referenced'])) {

            set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
        } elseif ($response == true) {

            set_alert('success', _l('deleted', _l('invoice_item')));
        } else {

            set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
        }

        redirect(admin_url('invoice_items'));
    }



    public function bulk_action()

    {

        hooks()->do_action('before_do_bulk_action_for_items');

        $total_deleted = 0;

        if ($this->input->post()) {

            $ids                   = $this->input->post('ids');

            $has_permission_delete = staff_can('delete',  'items');

            if (is_array($ids)) {

                foreach ($ids as $id) {

                    if ($this->input->post('mass_delete')) {

                        if ($has_permission_delete) {

                            if ($this->invoice_items_model->delete($id)) {

                                $total_deleted++;
                            }
                        }
                    }
                }
            }
        }



        if ($this->input->post('mass_delete')) {

            set_alert('success', _l('total_items_deleted', $total_deleted));
        }
    }



    public function search()

    {

        if ($this->input->post() && $this->input->is_ajax_request()) {

            echo json_encode($this->invoice_items_model->search($this->input->post('q')));
        }
    }



    /* Get item by id / ajax */

    public function get_item_by_id($id)

    {

        if ($this->input->is_ajax_request()) {

            $item                     = $this->invoice_items_model->get($id);

            $item->long_description   = nl2br($item->long_description);

            $item->custom_fields_html = render_custom_fields('items', $id, [], ['items_pr' => true]);

            $item->custom_fields      = [];



            $cf = get_custom_fields('items');



            foreach ($cf as $custom_field) {

                $val = get_custom_field_value($id, $custom_field['id'], 'items_pr');

                if ($custom_field['type'] == 'textarea') {

                    $val = clear_textarea_breaks($val);
                }

                $custom_field['value'] = $val;

                $item->custom_fields[] = $custom_field;
            }



            echo json_encode($item);
        }
    }



    /* Copy Item */

    public function copy($id)

    {

        if (staff_cant('create', 'items')) {

            access_denied('Create Item');
        }



        $data = (array) $this->invoice_items_model->get($id);



        $id = $this->invoice_items_model->copy($data);



        if ($id) {

            set_alert('success', _l('item_copy_success'));

            return redirect(admin_url('invoice_items?id=' . $id));
        }



        set_alert('warning', _l('item_copy_fail'));

        return redirect(admin_url('invoice_items'));
    }





    /* List all available ItemDivision */

    public function ItemDivision()

    {

        if (!has_permission_new('itemsdivision', '', 'view')) {

            access_denied('Invoice Items');
        }

        $data['table_data'] = $this->invoice_items_model->get_ItemDivision_data();

        $data['lastId'] = $this->invoice_items_model->get_last_recordItemDevision();

        $data['title'] = _l('ItemDivision');

        $this->load->view('admin/invoice_items/ItemDivision', $data);
    }

    /* Save New  Item Division / ajax */

    public function SaveItemDivision()

    {

        $data = array(

            'id' => $this->input->post('ItemDivisionID'),

            'name' => $this->input->post('ItemDivisionName'),

        );

        $itemDivision  = $this->invoice_items_model->SaveItemDivision($data);

        echo json_encode($itemDivision);
    }

    /* Get item Division Details by ItemID / ajax */

    public function GetItemDivisionDetailByID()

    {

        $ItemDivisionID = $this->input->post('ItemDivisionID');

        $itemDivisionDetails  = $this->invoice_items_model->getitemDivisionDetails($ItemDivisionID);

        echo json_encode($itemDivisionDetails);
    }

    /* Update Exiting Item Division / ajax */

    public function UpdateItemDivision()

    {

        $data = array(

            'name' => $this->input->post('ItemDivisionName'),

            'UserID2' => $this->session->userdata('username'),

            'Lupdate' => date('Y-m-d H:i:s'),

        );

        $ItemDivisionID = $this->input->post('ItemDivisionID');

        $itemDivision  = $this->invoice_items_model->UpdateItemDivision($data, $ItemDivisionID);

        echo json_encode($itemDivision);
    }





    /* List all available MAinitemGroups */

    public function MainGroups()

    {

        if (!has_permission_new('ItemMainGroup', '', 'view')) {

            access_denied('Item Main Group');
        }

        $data['RootCompany'] = $this->invoice_items_model->GetRootCompany();

        $data['table_data'] = $this->invoice_items_model->get_MainItemGroup_data();

        // var_dump(end($data['table_data'])['id']);die;
        $data['lastId'] = $this->invoice_items_model->get_last_recordMainItemGroup();

        $data['itemtype'] = $this->invoice_items_model->get_ItemType_data();

        $data['title'] = _l('ItemMainGroups');

        $this->load->view('admin/invoice_items/ItemMainGroup', $data);
    }

    /* Save New Main Item Group / ajax */

    public function SaveMainGroup()

    {


    $MainGroupName = $this->input->post('MainGroupName');
    $Prefix = $this->input->post('Prefix');
        if ($MainGroupName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Main Group Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('name', $MainGroupName);
        $exists = $this->db->get('items_main_groups')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Main Group Name already exists'
            ]);
            return;
        }

        $requiredDropdowns = [
			'ItemType'  => 'Please select Item Type'
		];

		foreach ($requiredDropdowns as $field => $errorMsg) {
			$value = $this->input->post($field);
			if (empty($value) || $value == '0') {
				echo json_encode([
					'status' => false,
					'message' => $errorMsg
				]);
				exit;
			}
		}
        if ($Prefix === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Prefix cannot be empty'
            ]);
            exit;
        }

        $data = array(

            'id' => $this->input->post('MainGroupCode'),

            'name' => strtoupper($this->input->post('MainGroupName')),

            'ItemTypeID' => $this->input->post('ItemType'),

            'prefix' => strtoupper($this->input->post('Prefix')),

            'IsActive' => $this->input->post('IsActive'),
            
            'Transdate' => date('Y-m-d H:i:s'),
            
            'UserID' => $this->session->userdata('username'),

        );

        $MainitemGroup  = $this->invoice_items_model->SaveMainItemGroup($data);

        echo json_encode($MainitemGroup);
    }

    //========================= Check Prefix Exit or not ===========================

    public function CheckPrefixExit()

    {

        $MainItemGroupPrefix = $this->input->post('Prefix');

        $PrefixDetails  = $this->invoice_items_model->CheckPrefixExit($MainItemGroupPrefix);

        echo json_encode($PrefixDetails);
    }

    /* Get Main item Group Details by ItemID / ajax */

    public function GetMainGroupDetailByID()

    {

        $ItemGroupID = $this->input->post('MainGroupCode');

        $row  = $this->invoice_items_model->getMainItemGroupDetails($ItemGroupID);

        if ($row) {
            echo json_encode([
                'MainGroupCode' => $row->id,
                'MainGroupName' => $row->name,
                'ItemType' => $row->ItemTypeID,
                'Prefix' => $row->prefix,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Update Exiting MainItemGroup / ajax */

    public function UpdateMainGroup()

    {
        $MainGroupName = $this->input->post('MainGroupName');
        $MainGroupCode = $this->input->post('MainGroupCode');
        if ($MainGroupName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Main Group Name cannot be empty'
            ]);
            exit;
        }
        // Duplicate name check (EXCEPT same ID)
        $this->db->where('name', $MainGroupName);
        $this->db->where('id !=', $MainGroupCode);
        $exists = $this->db->get('items_main_groups')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Main Group Name already exists'
            ]);
            exit;
        }

        $requiredDropdowns = [
            'ItemType'  => 'Please select Item Type'
        ];

        foreach ($requiredDropdowns as $field => $errorMsg) {
            $value = $this->input->post($field);
            if (empty($value) || $value == '0') {
                echo json_encode([
                    'status' => false,
                    'message' => $errorMsg
                ]);
                exit;
            }
        }


        $data = array(

            'name' => $this->input->post('MainGroupName'),

            'ItemTypeID' => $this->input->post('ItemType'),

            'prefix' => strtoupper($this->input->post('Prefix')),

            'IsActive' => $this->input->post('IsActive'),

            'UserID2' => $this->session->userdata('username'),

            'Lupdate' => date('Y-m-d H:i:s'),

        );

        $itemGroupID = $this->input->post('MainGroupCode');

        $itemGroupID = $this->invoice_items_model->UpdateMainItemGroup($data, $itemGroupID);

        echo json_encode($itemGroupID);
    }





    /* List all available itemGroups */

    public function ItemGroups()

    {

        if (!has_permission_new('ItemSubGroup1', '', 'view')) {

            access_denied('Invoice Items');
        }

        $data['MainItemGroup'] = $this->invoice_items_model->get_MainItemGroup_data();

        $data['table_data'] = $this->invoice_items_model->get_ItemGroup_data();

        $data['lastId'] = $this->invoice_items_model->get_last_recordItemGroup();

        $data['MainGroup'] = $this->invoice_items_model->get_main_groups();

        $data['title'] = _l('ItemGroups');

        $this->load->view('admin/invoice_items/ItemSubGroup1', $data);
    }

    /* Save New ItemID Group / ajax */

    public function SaveSubGroup()

    {


    $SubGroupName = $this->input->post('SubGroupName');
        if ($SubGroupName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Sub Group Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('name', $SubGroupName);
        $exists = $this->db->get('ItemsSubGroup1')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Sub Group Name already exists'
            ]);
            return;
        }

        $requiredDropdowns = [
			'MainGroup'  => 'Please select Main Group Name'
		];

		foreach ($requiredDropdowns as $field => $errorMsg) {
			$value = $this->input->post($field);
			if (empty($value) || $value == '0') {
				echo json_encode([
					'status' => false,
					'message' => $errorMsg
				]);
				exit;
			}
		}

        $data = array(

            'id' => $this->input->post('SubGroupCode'),

            'name' => $this->input->post('SubGroupName'),

            'main_group_id' => $this->input->post('MainGroup'),

            'IsActive' => $this->input->post('IsActive'),

            'Transdate' => date('Y-m-d H:i:s'),

            'UserID' => $this->session->userdata('username'),


        );

        $itemGroup  = $this->invoice_items_model->SaveItemGroup($data);

        echo json_encode($itemGroup);
    }

    /* Get item Group Details by ItemID / ajax */

    public function GetSubGroupDetailByID()

    {

        $ItemGroupID = $this->input->post('SubGroupCode');

        $row  = $this->invoice_items_model->getItemGroupDetails($ItemGroupID);

        if ($row) {
            echo json_encode([
                'SubGroupCode' => $row->id,
                'SubGroupName' => $row->name,
                'MainGroup' => $row->main_group_id,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Update Exiting ItemGroup / ajax */

    public function UpdateSubGroup()

    {
        $SubGroupName = $this->input->post('SubGroupName');
        if ($SubGroupName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Sub Group Name cannot be empty'
            ]);
            exit;
        }

    $requiredDropdowns = [
			'MainGroup'  => 'Please select Main Group Name'
		];

		foreach ($requiredDropdowns as $field => $errorMsg) {
			$value = $this->input->post($field);
			if (empty($value) || $value == '0') {
				echo json_encode([
					'status' => false,
					'message' => $errorMsg
				]);
				exit;
			}
		}

        $data = array(

            'name' => $this->input->post('SubGroupName'),

            'main_group_id' => $this->input->post('MainGroup'),

            'IsActive' => $this->input->post('IsActive'),

            'UserID2' => $this->session->userdata('username'),

            'Lupdate' => date('Y-m-d H:i:s'),

        );

        $itemGroupID = $this->input->post('SubGroupCode');

        $itemGroupID                     = $this->invoice_items_model->UpdateItemGroup($data, $itemGroupID);

        echo json_encode($itemGroupID);
    }









    /* List all available itemGroups */

    public function ItemSubGroups2()

    {

        if (!has_permission_new('itemssubgrp2', '', 'view')) {

            access_denied('Invoice Items');
        }

        $data['MainItemGroup'] = $this->invoice_items_model->get_MainItemGroup_data();

        $data['table_data'] = $this->invoice_items_model->get_ItemSubGroup2_data();

        $data['lastId'] = $this->invoice_items_model->get_last_recordItemGroup2();

        $data['MainGroup'] = $this->invoice_items_model->get_main_groups();

        // $data['subgroup1'] = $this->invoice_items_model->get_ItemGroup_data();


        $data['title'] = _l('ItemGroups');

        $this->load->view('admin/invoice_items/ItemSubGroups2', $data);
    }

    public function GetSubgroup1Data()

    {

        $MainItemGroup = $this->input->post('MainGroup');

        $Subgroup                    = $this->invoice_items_model->GetSubgroup1Data($MainItemGroup);

        echo json_encode($Subgroup);
    }

    /* Save New ItemID Subgroup2 / ajax */

    public function SaveItemSubGroup2()

    {

    $SubGroupName = $this->input->post('SubGroupName');
        if ($SubGroupName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Sub Group Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('name', $SubGroupName);
        $exists = $this->db->get('ItemsSubGroup2')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Sub Group Name already exists'
            ]);
            return;
        }

        $requiredDropdowns = [
			'MainGroup'  => 'Please select Main Group Name',
			'SubGroup1'  => 'Please select Sub Group 1'
		];

		foreach ($requiredDropdowns as $field => $errorMsg) {
			$value = $this->input->post($field);
			if (empty($value) || $value == '0') {
				echo json_encode([
					'status' => false,
					'message' => $errorMsg
				]);
				exit;
			}
		}

        $data = array(

            'name' => $this->input->post('SubGroupName'),

            'main_group_id' => $this->input->post('MainGroup'),

            'sub_group_id1' => $this->input->post('SubGroup1'),

        );

        $itemGroup  = $this->invoice_items_model->SaveItemSubGroup2($data);

        echo json_encode($itemGroup);
    }

    /* Get item SubGroup Details by ItemID / ajax */

    public function GetItemSubGroup2DetailByID()

    {

        $ItemGroupID = $this->input->post('SubGroupCode');

        $row  = $this->invoice_items_model->getItemSubGroup2Details($ItemGroupID);

        if ($row) {
            echo json_encode([
                'SubGroupCode' => $row->id,
                'SubGroupName' => $row->name,
                'MainGroup' => $row->main_group_id,
                'SubGroup1' => $row->sub_group_id1,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Update Exiting ItemSubGroup2 / ajax */

    public function UpdateItemSubGroup2()

    {
        $SubGroupName = $this->input->post('SubGroupName');
        if ($SubGroupName === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Sub Group Name cannot be empty'
            ]);
            exit;
        }

        $requiredDropdowns = [
			'MainGroup'  => 'Please select Main Group Name',
			'SubGroup1'  => 'Please select Sub Group 1'
		];

		foreach ($requiredDropdowns as $field => $errorMsg) {
			$value = $this->input->post($field);
			if (empty($value) || $value == '0') {
				echo json_encode([
					'status' => false,
					'message' => $errorMsg
				]);
				exit;
			}
		}

        $data = array(

            'name' => $this->input->post('SubGroupName'),

            'main_group_id' => $this->input->post('MainGroup'),

            'sub_group_id1' => $this->input->post('SubGroup1'),

            'IsActive' => $this->input->post('IsActive'),

            'UserID2' => $this->session->userdata('username'),

            'Lupdate' => date('Y-m-d H:i:s'),

        );

        $itemGroupID = $this->input->post('SubGroupCode');

        $itemGroupID                     = $this->invoice_items_model->UpdateItemSubGroup2($data, $itemGroupID);

        echo json_encode($itemGroupID);
    }
    //     public function add_item()
    // {
    //     $data['item_main_groups'] = $this->Invoice_items_model->get_item_main_groups();

    //     $this->load->view('item', $data);
    // }



}
