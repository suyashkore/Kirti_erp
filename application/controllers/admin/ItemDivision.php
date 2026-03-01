<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ItemDivision extends AdminController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Item_Division_Model');
    }
    public function index()
    {
        if (!has_permission_new('itemsdivision', '', 'view')) {
            access_denied('Items Division');
        }
        $data['lastId'] = $this->Item_Division_Model->get_last_recordItemDevision();
        $data['table_data'] = $this->Item_Division_Model->get_ItemDivision_data();
        $this->load->view('admin/ItemDivision/ItemDivision.php', $data);
    }

    /* Save New  Item Division / ajax */
    public function SaveItemDivision()
    {
        $name = $this->input->post('ItemDivisionName');
        if ($name === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Item Division Name cannot be empty'
            ]);
            exit;
        }
        // Check duplicate
        $this->db->where('name', $name);
        $exists = $this->db->get('ItemsDivisionMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Item Division Name already exists'
            ]);
            return;
        }
        $data = array(
            'id' => $this->input->post('ItemDivisionID'),
            'name' => $this->input->post('ItemDivisionName'),
            'IsActive' => $this->input->post('IsActive'),
        );
        $itemDivision  = $this->Item_Division_Model->SaveItemDivision($data);
        echo json_encode($itemDivision);
    }

    /* Get item Division Details by ItemID / ajax */
    public function GetItemDivisionDetailByID()
    {
        $ItemDivisionID = $this->input->post('ItemDivisionID');
        $row = $this->Item_Division_Model->getitemDivisionDetails($ItemDivisionID);

        $this->output->set_content_type('application/json');

        if ($row) {
            echo json_encode([
                'id'       => $row->id,
                'name'     => $row->name,
                'IsActive' => $row->IsActive
            ]);
        } else {
            echo json_encode(null);
        }
    }

    /* Update Exiting Item Division / ajax */
    public function UpdateItemDivision()
    {
        $ItemDivisionID = $this->input->post('ItemDivisionID');
        $name = $this->input->post('ItemDivisionName');
        if ($name === '') {
            echo json_encode([
                'status' => false,
                'message' => 'Item Division Name cannot be empty'
            ]);
            exit;
        }

        // Duplicate name check (EXCEPT same ID)
        $this->db->where('name', $name);
        $this->db->where('id !=', $ItemDivisionID);
        $exists = $this->db->get('ItemsDivisionMaster')->row();

        if ($exists) {
            echo json_encode([
                'status' => false,
                'message' => 'Item Division Name already exists'
            ]);
            exit;
        }

        $data = array(
            'name'     => $this->input->post('ItemDivisionName'),
            'IsActive' => $this->input->post('IsActive'),
        );
        $itemDivision  = $this->Item_Division_Model->UpdateItemDivision($data, $ItemDivisionID);
        echo json_encode($itemDivision);
    }
}
