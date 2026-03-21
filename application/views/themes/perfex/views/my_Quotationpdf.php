<?php
defined('BASEPATH') or exit('No direct script access allowed');

$inv = $invoice['data'];
$PlantDetail = GetPlantDetails($inv['PlantID'], $inv['FY']);
$historyItems = $inv['history'];

$pdf->SetMargins(5, 15, 5, 0);
$pdf->Ln(0);

// ================= STATUS MAPPING =================
$statusMap = [
    1 => 'Pending',
    2 => 'Cancel',
    3 => 'Expired',
    4 => 'Approved',
    5 => 'In-Progress',
    6 => 'Completed',
    7 => 'Partially Completed'
];
$statusLabel = isset($statusMap[(int)$inv['Status']])
    ? $statusMap[(int)$inv['Status']]
    : $inv['Status'];

$html = '<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:11px;">';

/* ================= HEADER () ================= */
$html .= '<thead>
<tr>
    <td colspan="9" align="center" style="font-size:14px;">
        <b>' . $PlantDetail->company_name . '<br>'
            . $PlantDetail->address . '<br>
            GSTIN: ' . $PlantDetail->gst . ' | Contact: ' . $PlantDetail->mobile1 . '<br>
            Email: ' . $PlantDetail->BusinessEmail . '</b>
    </td>
</tr>
<tr>
    <td colspan="9" align="center" style="font-size:13px;"><b>Purchase Quotation</b></td>
</tr>
</thead>';

$html .= '<tbody>';

/* ================= BASIC INFO () ================= */
$html .= '<tr>
    <td colspan="2"><b>Quotation No :</b> ' . $inv['QuotatioonID']                                   . '</td>
    <td colspan="2"><b>Trans Date :</b> '   . date('d/m/Y', strtotime($inv['TransDate']))            . '</td>
    <td colspan="3"><b>Payment Terms :</b> '. $inv['PaymentTerms']                                   . '</td>
    <td colspan="2"><b>Freight Terms :</b> '. $inv['FreightTermsname']                                   . '</td>
</tr>';

$html .= '<tr>
    <td colspan="2"><b>Delivery From :</b> '. date('d/m/Y', strtotime($inv['DeliveryFrom']))         . '</td>
    <td colspan="2"><b>Delivery To :</b> '  . date('d/m/Y', strtotime($inv['DeliveryTo']))           . '</td>
    <td colspan="3"><b>Category :</b> '     . $inv['category_name']                                  . '</td>
    <td colspan="2"><b>Item Type :</b> '    . $inv['ItemTypeName']                                       . '</td>
</tr>';

$html .= '<tr>
    <td colspan="4"><b>Vendor Name :</b> '  . htmlspecialchars($inv['company'])                      . '</td>
    <td colspan="3"><b>GSTIN :</b> '        . ($inv['GSTIN'] ? $inv['GSTIN'] : 'N/A')               . '</td>
    <td colspan="3"><b>Status :</b> '       . $statusLabel                                           . '</td>
</tr>';

/* ================= ITEM TABLE HEADER () ================= */
$html .= '<tr style="background-color:#f2f2f2;">
    <td width="5%"  align="center"><b>Sr.</b></td>
    <td width="10%" align="center"><b>Item ID</b></td>
    <td width="25%" align="center"><b>Name</b></td>
    <td width="8%"  align="center"><b>Unit</b></td>
    <td width="10%" align="center"><b>Basic Rate</b></td>
    <td width="10%" align="center"><b>Order Qty</b></td>
    <td width="10%" align="center"><b>Disc Amt</b></td>
    <td width="8%"  align="center"><b>GST %</b></td>
    <td width="14%" align="center"><b>Net Amt</b></td>
</tr>';

/* ================= ITEM ROWS LOOP ( items ) ================= */
foreach ($historyItems as $index => $item) {

    $discAmt = floatval($item['DiscAmt']);
    $netAmt  = floatval($item['NetOrderAmt']);
    $gst     = $item['cgst'] + $item['sgst'];

    $html .= '<tr>
        <td width="5%"  align="center">' . ($index + 1)                                . '</td>
        <td width="10%" align="center">' . htmlspecialchars($item['ItemID'])            . '</td>
        <td width="25%" align="left">'   . htmlspecialchars($item['ItemName'])          . '</td>  <!-- ✅ Fix: ItemName -->
        <td width="8%"  align="center">' . $item['SuppliedIn']                          . '</td>
        <td width="10%" align="right">'  . number_format($item['BasicRate'], 2)         . '</td>
        <td width="10%" align="center">' . number_format($item['OrderQty'], 3)          . '</td>
        <td width="10%" align="right">'  . number_format($discAmt, 2)                  . '</td>
        <td width="8%"  align="center">' . $gst                                        . '</td>
        <td width="14%" align="right">'  . number_format($netAmt, 2)                   . '</td>
    </tr>';
}

$html .= '</tbody></table>';

/* ================= SUMMARY ( loop ) ================= */
$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-size:11px;">
    <tr>
        <td width="55%" style="border-left:1px solid #000; border-bottom:1px solid #000;"></td>
        <td width="45%" style="padding:0;">
            <table border="1" cellpadding="6" cellspacing="0" width="100%"
                   style="font-size:11px; border-collapse:collapse;">
                <tr><td align="right">Item Amount</td>
                    <td align="right">' . number_format($inv['ItemAmt'], 2) . '</td></tr>
                <tr><td align="right">Discount</td>
                    <td align="right">' . number_format($inv['DiscAmt'], 2) . '</td></tr>
                <tr><td align="right">Taxable Amount</td>
                    <td align="right">' . number_format($inv['TaxableAmt'], 2) . '</td></tr>
                <tr><td align="right">CGST</td>
                    <td align="right">' . number_format($inv['CGSTAmt'], 2) . '</td></tr>
                <tr><td align="right">SGST</td>
                    <td align="right">' . number_format($inv['SGSTAmt'], 2) . '</td></tr>
                <tr><td align="right">IGST</td>
                    <td align="right">' . number_format($inv['IGSTAmt'], 2) . '</td></tr>
                <tr><td align="right">Round Off</td>
                    <td align="right">' . number_format($inv['RoundOffAmt'], 2) . '</td></tr>
                <tr><td align="right"><b>Net Amount</b></td>
                    <td align="right"><b>' . number_format($inv['NetAmt'], 2) . '</b></td></tr>
            </table>
        </td>
    </tr>
</table>';

/* ================= FOOTER  ================= */
$html .= '<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:11px;">
    <tr>
        <td width="50%" style="height:30px; vertical-align:bottom;"><b>Prepared By :</b><br></td>
        <td width="50%" style="height:30px; vertical-align:bottom;"><b>Authorized Sign :</b><br></td>
    </tr>
</table>';

$pdf->writeHTML($html, true, false, false, false, '');