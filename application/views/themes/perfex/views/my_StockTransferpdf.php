<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();
$pdf->SetMargins(5, 15, 5, 0);
$pdf->Ln(0);

$inv     = $invoice['invoice'];   // stdClass object
$history = $invoice['history'];   // Array of items

$PlantDetail = GetPlantDetails($inv->TransferID, $inv->FY);

$html = '<table width="100%" border="0" cellpadding="4" cellspacing="0" style="font-size:11px;">';

// ===== HEADER =====
$html .= '<thead>';
$html .= '<tr>
    <td colspan="7" align="center" style="font-size:14px; border:1px solid #000;">
        <b>' . $PlantDetail->company_name . '<br>' . $PlantDetail->address . '<br>GSTIN: ' . $PlantDetail->gst . ' | Contact: ' . $PlantDetail->mobile1 . '<br>Email: ' . $PlantDetail->BusinessEmail . '</b>
    </td>
</tr>';
$html .= '<tr>
    <td colspan="7" align="center" style="font-size:13px; border:1px solid #000;"><b>Stock Transfer</b></td>
</tr>';
$html .= '</thead>';

$html .= '<tbody>';

// ===== ROW 1: Transfer ID & Date =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="2"><b>Transfer ID :</b> ' . $inv->TransferID . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>Transfer Date :</b> ' . date('d-m-Y', strtotime($inv->TransferDate)) . '</td>
    <td style="border:1px solid #000;" colspan="3"></td>
</tr>';

// ===== ROW 2: FROM & TO =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="2"><b>From Location :</b> ' . $inv->FromLocation . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>From Godown :</b> ' . $inv->FromGodown . '</td>
    <td style="border:1px solid #000;" colspan="1"><b>To Location :</b> ' . $inv->ToLocation . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>To Godown :</b> ' . $inv->ToGodown . '</td>
</tr>';

// ===== ROW 3: Vehicle & Transport =====
$html .= '<tr>
    <td style="border:1px solid #000;" colspan="2"><b>Driver Name :</b> ' . $inv->DriverName . '</td>
    <td style="border:1px solid #000;" colspan="2"><b>Vehicle Number :</b> ' . $inv->VehicleNo . '</td>
    <td style="border:1px solid #000;" colspan="3"><b>Total Distance :</b> ' . $inv->Distance . ' Km</td>
</tr>';

// ===== ROW 4: E WAY BILL Info - Only if EwayBill is Y =====
if (isset($inv->isEwayBill) && $inv->isEwayBill === 'Y') {
    $html .= '<tr>
        <td style="border:1px solid #000;" colspan="2"><b>E Way Bill No :</b> ' . $inv->EwayBillNo . '</td>
        <td style="border:1px solid #000;" colspan="2"><b>E Way Bill Date :</b> ' . date('d-m-Y', strtotime($inv->EwayBillDate)) . '</td>
        <td style="border:1px solid #000;" colspan="3"><b>E Way Bill Expiry Date :</b> ' . date('d-m-Y', strtotime($inv->EwayBillExpDate)) . '</td>
    </tr>';
}

// ===== Item Table Header =====
$html .= '<tr style="background-color:#f2f2f2;">
    <td style="border:1px solid #000; width:5%;"  align="center"><b>Sr.</b></td>
    <td style="border:1px solid #000; width:30%;" align="center"><b>Item Name</b></td>
    <td style="border:1px solid #000; width:15%;" align="center"><b>HSN Code</b></td>
    <td style="border:1px solid #000; width:10%;" align="center"><b>UOM</b></td>
    <td style="border:1px solid #000; width:13%;" align="center"><b>Pack Qty</b></td>
    <td style="border:1px solid #000; width:13%;" align="center"><b>Pack Wt (kg)</b></td>
    <td style="border:1px solid #000; width:14%;" align="center"><b>Transferred Qty</b></td>
</tr>';

// ===== Item Rows from history =====
if (!empty($history)) {
    foreach ($history as $item) {
        $html .= '<tr style="height:25px;">
            <td style="border:1px solid #000;" align="center">' . $item['Ordinalno'] . '</td>
            <td style="border:1px solid #000; padding-left:4px;">' . htmlspecialchars($item['ItemName']) . '</td>
            <td style="border:1px solid #000;" align="center">' . htmlspecialchars($item['hsn_code']) . '</td>
            <td style="border:1px solid #000;" align="center">' . htmlspecialchars($item['SuppliedIn']) . '</td>
            <td style="border:1px solid #000;" align="center">' . number_format($item['CaseQty'], 2) . '</td>
            <td style="border:1px solid #000;" align="center">' . number_format($item['UnitWeight'], 2) . '</td>
            <td style="border:1px solid #000;" align="center">' . number_format($item['OrderQty'], 2) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr style="height:25px;">
        <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>';
}

// ===== Summary =====
$summary = [
    'Total Weight' => number_format($inv->TotalWeight, 0),
    'Total Qty'    => number_format($inv->TotalQuantity, 0),
];

foreach ($summary as $label => $value) {
    $html .= '<tr>
        <td colspan="5" style="border-left:1px solid #000; border-bottom:1px solid #000;"></td>
        <td style="border:1px solid #000;" align="right"><b>' . $label . '</b></td>
        <td style="border:1px solid #000;" align="right"><b>' . $value . '</b></td>
    </tr>';
}

// ===== Footer =====
$html .= '<tr style="height:60px;">
    <td style="border:1px solid #000;" colspan="3" rowspan="3"><b>Prepared By :</b></td>
    <td style="border:1px solid #000;" colspan="4" rowspan="3"><b>Authorized Sign :</b></td>
</tr>
<tr style="height:50px;">
    <td style="border:1px solid #000;" colspan="3"></td>
    <td style="border:1px solid #000;" colspan="4"></td>
</tr>
<tr style="height:50px;">
    <td style="border:1px solid #000;" colspan="3"></td>
    <td style="border:1px solid #000;" colspan="4"></td>
</tr>';

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');