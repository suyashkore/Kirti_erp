<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Purchase_order_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
            [
                'name'      => 'PO number',
                'key'       => '{po_number}',
                'available' => [
                    'purchase_order',
                ],
            ],
            [
                'name'      => 'Type',
                'key'       => '{type}',
                'available' => [
                    'purchase_order',
                ],
            ],
            [
                'name'      => 'Public link',
                'key'       => '{public_link}',
                'available' => [
                    'purchase_order',
                ],
            ],
            [
                'name'      => 'PO name',
                'key'       => '{po_name}',
                'available' => [
                    'purchase_order',
                ],
            ],
            [
                'name'      => 'PO tax value',
                'key'       => '{po_tax_value}',
                'available' => [
                    'purchase_order',
                ],
            ],
            [
                'name'      => 'PO subtotal',
                'key'       => '{po_subtotal}',
                'available' => [
                    'purchase_order',
                ],
            ],
            [
                'name'      => 'PO value',
                'key'       => '{po_value}',
                'available' => [
                    'purchase_order',
                ],
            ],
        ];
    }

    /**
     * Merge field for appointments
     * @param  mixed $teampassword 
     * @return array
     */
    public function format($po_id)
    {
        $this->ci->load->model('purchase/purchase_model');


        $fields = [];

        $this->ci->db->where('id', $po_id);

        $po = $this->ci->db->get(db_prefix() . 'pur_orders')->row();


        if (!$po) {
            return $fields;
        }

        $fields['{type}']                  =  _l($po->type) ;
        $fields['{public_link}']                  = site_url('purchase/vendors_portal/pur_order/' . $po->id.'/'.$po->hash);
        $fields['{po_name}']                  =  $po->pur_order_name;
        $fields['{po_number}']                  =  $po->pur_order_number;
        $fields['{po_value}']                   =  app_format_money($po->total, '');
        $fields['{po_tax_value}']                   =  app_format_money($po->total_tax, '');
        $fields['{po_subtotal}']                   =  app_format_money($po->subtotal, '');

        return $fields;
    }
}
