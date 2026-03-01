<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ItemDivision extends AdminController {

    public function __construct() {
        parent::__construct();
        $this->load->model('Item_Division_Model');
    }
    public function index() {
        if (!has_permission_new('itemsdivision', '', 'view')) {
				access_denied('Items Division');
			}
            $data['lastId'] = $this->Item_Division_Model->get_last_recordItemDevision();
            $data['table_data'] = $this->Item_Division_Model->get_ItemDivision_data();
        $this->load->view('admin/ItemDivision/ItemDivision.php',$data);
    }

    /* Save New  Item Division / ajax */
		public function SaveItemDivision()
		{
			$data = array(
            'id'=>$this->input->post('ItemDivisionID'),
            'name'=>$this->input->post('ItemDivisionName'),
            'IsActive'=>$this->input->post('Blocked'),
			);
			$itemDivision  = $this->Item_Division_Model->SaveItemDivision($data);
			echo json_encode($itemDivision);
		}

        /* Get item Division Details by ItemID / ajax */
		public function GetItemDivisionDetailByID()
		{
			// $ItemDivisionID = $this->input->post('ItemDivisionID');
			// $itemDivisionDetails  = $this->Item_Division_Model->getitemDivisionDetails($ItemDivisionID);
			// echo json_encode($itemDivisionDetails);

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
			$data = array(
			'name'     => $this->input->post('ItemDivisionName'),
        	'IsActive' => $this->input->post('Blocked'),
            // 'id'=>$this->session->userdata('username'),
            // 'Lupdate'=>date('Y-m-d H:i:s'),
			);
			$itemDivision  = $this->Item_Division_Model->UpdateItemDivision($data,$ItemDivisionID);
			echo json_encode($itemDivision);
		}

}
