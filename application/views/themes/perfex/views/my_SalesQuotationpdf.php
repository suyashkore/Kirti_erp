<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();
$pdf->SetMargins(5, 15, 5, 0);
$pdf->Ln(0);

$inv     = $invoice['invoice'];   // stdClass object
$history = $invoice['history'];   // Array of items

$PlantDetail = GetPlantDetails($inv->QuotationID, $inv->FY);
// echo '<pre>'; print_r($invoice); echo '</pre>'; exit();

$html = '<table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:11px;">';


// ===== HEADER =====
$html .= '<thead>';
$html .= '<tr>
    <td colspan="10" align="center" style="font-size:14px; border:1px solid #000;">
        <b>' . $PlantDetail->company_name . '<br>' . $PlantDetail->address . '<br>GSTIN: ' . $PlantDetail->gst . ' | Contact: ' . $PlantDetail->mobile1 . '<br>Email: ' . $PlantDetail->BusinessEmail . '</b>
    </td>
</tr>';
$html .= '<tr>
    <td colspan="10" align="center" style="font-size:13px; border:1px solid #000;"><b>Sales Quotation</b></td>
</tr>';
$html .= '</thead>';

$html .= '<tbody>';

// ===== ROW 1: Center & PO Info =====
// ===== ROW 1: SO Info =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="2"><b>Quotation No :</b> ' . $inv->QuotationID . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>Quotation Date :</b> ' . date('d-m-Y', strtotime($inv->TransDate)) . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Item/Service :</b> ' . $inv->ItemTypeName . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Center State :</b> ' . $inv->state_name . '</td>
</tr>';

// ===== ROW 2: Category & Terms =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="2"><b>Order Category :</b> ' . $inv->CategoryName . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>Payment Terms :</b> ' . $inv->PaymentTerms . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Sales Location :</b> ' . $inv->LocationName  . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Freight Terms :</b> ' . $inv->FreightTerms . '</td>
</tr>';

// ===== ROW 3: Quotation & Delivery =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="2"><b>Quotation No :</b> ' . $inv->QuotationID . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>Order Date :</b> ' . date('d-m-Y', strtotime($inv->TransDate)) . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Delivery From :</b> ' . date('d-m-Y', strtotime($inv->DeliveryFrom)) . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Delivery To :</b> ' . date('d-m-Y', strtotime($inv->DeliveryTo)) . '</td>
</tr>';

// ===== ROW 4: Customer =====
$html .= '<tr style="border:1px solid #000;">
    <td style="border:1px solid #000;" colspan="2"><b>GST :</b> ' . $inv->GSTIN . '</td>
    <td style="border:1px solid #000;" colspan="8"><b>Broker Name :</b> ' . $inv->BrokerName . ' - ('. $inv->BrokerID.')</td>
</tr>';

// ===== ROW 5: Customer Info =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="5"><b>Customer Name :</b> ' . $inv->company . '</td>
    <td style="border:1px solid #000;" colspan="6"><b>Address :</b> ' . $inv->billing_address . '</td>
</tr>';

// ===== Item Table Header =====
$html .= '<tr style="background-color:#f2f2f2;">
    <td style="border:1px solid #000;" align="center"><b>Sr.</b></td>
    <td style="border:1px solid #000;" align="center"><b>Item ID</b></td>
    <td style="border:1px solid #000;" align="center"><b>UOM</b></td>
    <td style="border:1px solid #000;" align="center"><b>Pack Qty</b></td>
    <td style="border:1px solid #000;" align="center"><b>Pack Wt (kg)</b></td>
    <td style="border:1px solid #000;" align="center"><b>Sales Unit Qty</b></td>
    <td style="border:1px solid #000;" align="center"><b>Basic Rate</b></td>
    <td style="border:1px solid #000;" align="center"><b>Disc Amt</b></td>
    <td style="border:1px solid #000;" align="center"><b>GST %</b></td>
    <td style="border:1px solid #000;" align="center"><b>Net Amount</b></td>
</tr>';

// ===== Item Rows from history =====
if (!empty($history)) {
    foreach ($history as $item) {

        $cgst = floatval($item['cgst']);
        $sgst = floatval($item['sgst']);
        $igst = floatval($item['igst']);

        if ($cgst > 0 || $sgst > 0) {
            $total_gst = $cgst + $sgst;
            $gst_label = $total_gst . '%';
        } elseif ($igst > 0) {
            $gst_label = $igst . '%';
        } else {
            $gst_label = '0%';
        }

        $html .= '<tr style="height:25px;">
            <td style="border:1px solid #000;" align="center">' . $item['Ordinalno']+1 . '</td>
            <td style="border:1px solid #000;">' . htmlspecialchars($item['ItemName']) . '</td>
            <td style="border:1px solid #000;" align="center">' . htmlspecialchars($item['SuppliedIn']) . '</td>
            <td style="border:1px solid #000;" align="center">' . number_format($item['CaseQty'], 2) . '</td>
            <td style="border:1px solid #000;" align="center">' . number_format($item['UnitWeight'], 2) . '</td>
            <td style="border:1px solid #000;" align="center">' . number_format($item['OrderQty'], 2) . '</td>
            <td style="border:1px solid #000;" align="right">' . number_format($item['BasicRate'], 2) . '</td>
            <td style="border:1px solid #000;" align="right">' . number_format($item['DiscAmt'], 2) . '</td>
            <td style="border:1px solid #000;" align="center">' . $gst_label . '</td>
            <td style="border:1px solid #000;" align="right">' . number_format($item['NetOrderAmt'], 2) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr style="height:25px;">
        <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>';
}

// ===== Summary =====
$summary = [
    'Item Total'   => number_format($inv->ItemAmt,   2),
    'Taxable Amt'  => number_format($inv->TaxableAmt, 2),
    'CGST Amt'     => number_format($inv->CGSTAmt,    2),
    'SGST Amt'     => number_format($inv->SGSTAmt,    2),
    'IGST Amt'     => number_format($inv->IGSTAmt,    2),
    'Net Amt'      => number_format($inv->NetAmt,     2),
];

// $html .= '<tr>
//     <td colspan="8" style="border:1px solid #000;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Total Wt (Kg):</b> ' . number_format($inv->TotalWeight, 2) . ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <b>Total Qty:</b> ' . number_format($inv->TotalQuantity, 2) . '</td>

// </tr>';

// foreach ($summary as $label => $value) {
//     $bold    = ($label === 'Net Amt') ? '<b>' : '';
//     $boldEnd = ($label === 'Net Amt') ? '</b>' : '';
//     $html .= '<tr>
//         <td style="border-left:1px solid #000;" colspan="8"></td>
//         <td style="border:1px solid #000;" align="right">' . $bold . $label . $boldEnd . '</td>
//         <td style="border:1px solid #000;" align="right">' . $bold . $value . $boldEnd . '</td>
//     </tr>';
// }

$first = true;

foreach ($summary as $label => $value) {

    $bold    = ($label === 'Net Amt') ? '<b>' : '';
    $boldEnd = ($label === 'Net Amt') ? '</b>' : '';

    if ($first) {

        // First row includes Total Wt & Qty
        $html .= '<tr>
            <td colspan="8" style="border:1px solid #000;">
                <b>Total Wt (Kg):</b> ' . number_format($inv->TotalWeight, 2) . '
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <b>Total Qty:</b> ' . number_format($inv->TotalQuantity, 2) . '
            </td>
            <td style="border:1px solid #000;" align="right">' . $bold . $label . $boldEnd . '</td>
            <td style="border:1px solid #000;" align="right">' . $bold . $value . $boldEnd . '</td>
        </tr>';

        $first = false;

    } else {

        $html .= '<tr>
            <td colspan="8" style="border-left:1px solid #000; border-right:1px solid #000;"></td>
            <td style="border:1px solid #000;" align="right">' . $bold . $label . $boldEnd . '</td>
            <td style="border:1px solid #000;" align="right">' . $bold . $value . $boldEnd . '</td>
        </tr>';
    }
}


// ===== Footer =====
$html .= '<tr style="height:50px;">
    <td style="border:1px solid #000;" colspan="5"><b>Prepared By :</b></td>
    <td style="border:1px solid #000;" colspan="5"><b>Authorized Sign :</b></td>
</tr>';

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');
