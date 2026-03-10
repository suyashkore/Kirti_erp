<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();
$pdf->SetMargins(5, 15, 5, 0);
$pdf->Ln(0);

$inv     = $invoice['invoice'];   // stdClass object
$history = $invoice['history'];   // Array of items

$PlantDetail = GetPlantDetails($inv->OrderID, $inv->FY);

$html = '<table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:11px;">';


// ===== HEADER =====
$html .= '<thead>';
$html .= '<tr>
    <td colspan="11" align="center" style="font-size:14px; border:1px solid #000;">
        <b>' . $PlantDetail->company_name . '<br>' . $PlantDetail->address . '<br>GSTIN: ' . $PlantDetail->gst . ' | Contact: ' . $PlantDetail->mobile1 . '<br>Email: ' . $PlantDetail->BusinessEmail . '</b>
    </td>
</tr>';
$html .= '<tr>
    <td colspan="11" align="center" style="font-size:13px; border:1px solid #000;"><b>Sales Invoice</b></td>
</tr>';
$html .= '</thead>';

$html .= '<tbody>';

// ===== ROW 1: SO Info =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="2"><b>Invoice No :</b> ' . $inv->InvoiceID . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Invoice Date :</b> ' . date('d-m-Y', strtotime($inv->InvoiceDate)) . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>DO No :</b> ' . $inv->DeliveryOrderID . '</td>
    <td style="border:1px solid #000;" colspan="4"><b>Delivery Date :</b> ' . date('d-m-Y', strtotime($inv->DODate)) . '</td>
</tr>';

// ===== ROW 2: Category & Terms =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="2"><b>Order Category :</b> ' . $inv->CategoryName . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Dispatch From :</b> ' . $inv->LocationName  . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>GST :</b> ' . $inv->GSTIN . '</td>
    <td style="border:1px solid #000;" colspan="4"><b>Gate Entry No :</b> ' . $inv->GateINID . '</td>
</tr>';

// ===== ROW 3: Vehicle & Transport =====
$html .= '<tr style="border:1px solid #000;">
    <td style="border:1px solid #000;" colspan="2"><b>Vehicle Number :</b> ' . $inv->VehicleNo . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Transporter ID :</b> ' . $inv->TransporterID . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>Driver Name :</b> ' . $inv->DriverName . '</td>
    <td style="border:1px solid #000;" colspan="4"><b>Customer Location :</b> ' . $inv->ShippingCityName . '</td>
</tr>';

// ===== ROW 4: Customer Info =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="5"><b>Customer Name :</b> ' . $inv->company . '</td>
    <td style="border:1px solid #000;" colspan="6"><b>Address :</b> ' . $inv->billing_address . '</td>
</tr>';

// ===== Item Table Header =====
$html .= '<tr style="background-color:#f2f2f2;">
    <td style="border:1px solid #000;" align="center"><b>Sr.</b></td>
    <td style="border:1px solid #000;" align="center"><b>Item ID</b></td>
    <td style="border:1px solid #000;" align="center"><b>HSN Code</b></td>
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
            <td style="border:1px solid #000;" align="center">' . $item['Ordinalno'] . '</td>
            <td style="border:1px solid #000;">' . htmlspecialchars($item['ItemName']) . '</td>
            <td style="border:1px solid #000;" align="center">' . htmlspecialchars($item['hsn_code']) . '</td>
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
        <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
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

$first = true;

foreach ($summary as $label => $value) {

    $bold    = ($label === 'Net Amt') ? '<b>' : '';
    $boldEnd = ($label === 'Net Amt') ? '</b>' : '';

    if ($first) {

        // First row includes Total Wt & Qty — colspan updated to 9 for 11-column table
        $html .= '<tr>
            <td colspan="9" style="border:1px solid #000;">
                <b>Total Wt (Kg):</b> ' . number_format($inv->TotalWeight, 2) . '
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <b>Total Qty:</b> ' . number_format($inv->TotalQuantity, 2) . '
            </td>
            <td style="border:1px solid #000;" align="right">' . $bold . $label . $boldEnd . '</td>
            <td style="border:1px solid #000;" align="right">' . $bold . $value . $boldEnd . '</td>
        </tr>';

        $first = false;

    } else {

        // colspan updated to 9 for 11-column table
        $html .= '<tr>
            <td colspan="9" style="border-left:1px solid #000; border-right:1px solid #000;"></td>
            <td style="border:1px solid #000;" align="right">' . $bold . $label . $boldEnd . '</td>
            <td style="border:1px solid #000;" align="right">' . $bold . $value . $boldEnd . '</td>
        </tr>';
    }
}

$html .= '<tr>
    <td colspan="11" align="center" style="border:1px solid #000; font-size:12px;">
        <b>GST BREAKUP DETAILS (AMOUNT IN Rs.)</b>
    </td>
</tr>';

$html .= '<tr style="background-color:#f2f2f2;">
    <td style="border:1px solid #000;" colspan="2" align="center"><b>ITEM</b></td>
    <td style="border:1px solid #000;" align="center"><b>HSN CODE</b></td>
    <td style="border:1px solid #000;" colspan="2" align="center"><b>TAXABLE AMOUNT (Rs.)</b></td>
    <td style="border:1px solid #000;" align="center"><b>CGST (Rs.)</b></td>
    <td style="border:1px solid #000;" align="center"><b>SGST (Rs.)</b></td>
    <td style="border:1px solid #000;" colspan="2" align="center"><b>IGST (Rs.)</b></td>
    <td style="border:1px solid #000;" colspan="2" align="center"><b>TOTAL (Rs.)</b></td>
</tr>';

// Group items by GST category
$gst_breakup = [];

if (!empty($history)) {
    foreach ($history as $item) {
        $cgst = floatval($item['cgst']);
        $sgst = floatval($item['sgst']);
        $igst = floatval($item['igst']);

        // Use ItemName + ItemID as unique key
        $key = $item['ItemName'] . '||' . $item['hsn_code'] . '||' . $item['Ordinalno'];

        if (!isset($gst_breakup[$key])) {
            $gst_breakup[$key] = [
                'item_label' => htmlspecialchars($item['ItemName']) . ' (' . htmlspecialchars($item['ItemID']) . ')',
                'hsn_code'   => htmlspecialchars($item['hsn_code']),
                'taxable'    => 0,
                'cgst'       => $cgst,
                'sgst'       => $sgst,
                'igst'       => $igst,
                'cgst_amt'   => 0,
                'sgst_amt'   => 0,
                'igst_amt'   => 0,
                'total'      => 0,
            ];
        }

        $taxable  = floatval($item['NetOrderAmt']) - (floatval($item['NetOrderAmt']) * ($cgst + $sgst + $igst) / (100 + $cgst + $sgst + $igst));
        $cgst_amt = $taxable * $cgst / 100;
        $sgst_amt = $taxable * $sgst / 100;
        $igst_amt = $taxable * $igst / 100;

        $gst_breakup[$key]['taxable']  += $taxable;
        $gst_breakup[$key]['cgst_amt'] += $cgst_amt;
        $gst_breakup[$key]['sgst_amt'] += $sgst_amt;
        $gst_breakup[$key]['igst_amt'] += $igst_amt;
        $gst_breakup[$key]['total']    += floatval($item['NetOrderAmt']);
    }
}

$grand_taxable  = 0;
$grand_cgst_amt = 0;
$grand_sgst_amt = 0;
$grand_igst_amt = 0;
$grand_total    = 0;

$sr = 1;
foreach ($gst_breakup as $label => $row) {
    $html .= '<tr>
        <td style="border:1px solid #000;" colspan="2">' . $row['item_label'] . '</td>
        <td style="border:1px solid #000;" align="center">' . $row['hsn_code'] . '</td>
        <td style="border:1px solid #000;" colspan="2" align="right">' . number_format($row['taxable'], 2) . '</td>
        <td style="border:1px solid #000;" align="right">' . number_format($row['cgst_amt'], 2) . '</td>
        <td style="border:1px solid #000;" align="right">' . number_format($row['sgst_amt'], 2) . '</td>
        <td style="border:1px solid #000;" colspan="2" align="right">' . number_format($row['igst_amt'], 2) . '</td>
        <td style="border:1px solid #000;" colspan="2" align="right">' . number_format($row['total'], 2) . '</td>
    </tr>';
 
    $grand_taxable  += $row['taxable'];
    $grand_cgst_amt += $row['cgst_amt'];
    $grand_sgst_amt += $row['sgst_amt'];
    $grand_igst_amt += $row['igst_amt'];
    $grand_total    += $row['total'];

    $sr++;
}

// TOTAL Row
$html .= '<tr style="background-color:#f2f2f2;">
    <td style="border:1px solid #000;" colspan="3" align="right"><b>TOTAL</b></td>
    <td style="border:1px solid #000;" colspan="2" align="right"><b>' . number_format($grand_taxable, 2) . '</b></td>
    <td style="border:1px solid #000;" align="right"><b>' . number_format($grand_cgst_amt, 2) . '</b></td>
    <td style="border:1px solid #000;" align="right"><b>' . number_format($grand_sgst_amt, 2) . '</b></td>
    <td style="border:1px solid #000;" colspan="2" align="right"><b>' . number_format($grand_igst_amt, 2) . '</b></td>
    <td style="border:1px solid #000;" colspan="2" align="right"><b>' . number_format($grand_total, 2) . '</b></td>
</tr>';


// ===== Footer =====
$html .= '<tr style="height:50px;">
    <td style="border:1px solid #000;" colspan="5" rowspan="3"><b>Prepared By :</b></td>
    <td style="border:1px solid #000;" colspan="6" rowspan="3"><b>Authorized Sign :</b></td>
</tr>
<tr style="height:50px;">
    <td style="border:1px solid #000;" colspan="5"></td>
    <td style="border:1px solid #000;" colspan="6"></td>
</tr>
<tr style="height:50px;">
    <td style="border:1px solid #000;" colspan="5"></td>
    <td style="border:1px solid #000;" colspan="6"></td>
</tr>';

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');