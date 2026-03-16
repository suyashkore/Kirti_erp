<?php
defined('BASEPATH') or exit('No direct script access allowed');

$inv     = $invoice['data'][0];
$history = $invoice['data1'];

$PlantDetail = GetPlantDetails($inv['OrderID'], $inv['FY']);

// ================= LOOP THROUGH EACH HISTORY RECORD =================
foreach ($history as $index => $item) {

    // Add new page for each record (except first)
    if ($index > 0) {
        $pdf->AddPage();
    }

    $pdf->SetMargins(5, 15, 5, 0);
    $pdf->Ln(0);

    $html = '<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:11px;">';

    /* ================= HEADER ================= */
    $html .= '<thead>
    <tr>
        <td colspan="10" align="center" style="font-size:14px;">
            <b>' . $PlantDetail->company_name . '<br>'
                . $PlantDetail->address . '<br>
                GSTIN: ' . $PlantDetail->gst . ' | Contact: ' . $PlantDetail->mobile1 . '<br>
                Email: ' . $PlantDetail->BusinessEmail . '</b>
        </td>
    </tr>
    <tr>
        <td colspan="10" align="center" style="font-size:13px;"><b>Mandi Purchase Order</b></td>
    </tr>
    </thead>';

    $html .= '<tbody>';

    /* ================= BASIC INFO ================= */
    $html .= '<tr>
        <td colspan="3"><b>PO No :</b> '       . $inv['OrderID']                                      . '</td>
        <td colspan="3"><b>PO Date :</b> '     . date('d/m/Y', strtotime($inv['OrderDate']))          . '</td>
        <td colspan="2"><b>Vehicle No :</b> '  . $inv['VehicleNo']                                    . '</td>
        <td colspan="2"><b>Trans Date :</b> '  . date('d/m/Y', strtotime($inv['TransDate']))          . '</td>
    </tr>';

    $html .= '<tr>
        <td colspan="3"><b>Warehouse :</b> '   . $inv['WarehouseID']                                  . '</td>
        <td colspan="3"><b>Item Name :</b> '   . $inv['ItemID']                                       . '</td>
        <td colspan="2"><b>TDS Name :</b> '    . $inv['TDSCode']                                      . '</td>
        <td colspan="2"><b>Center Loc :</b> '  . $inv['CenterLocation']                               . '</td>
    </tr>';

    /* ================= HISTORY HEADER ================= */
    $html .= '<tr style="background-color:#f2f2f2;">
        <td width="4%"  align="center"><b>Sr.</b></td>
        <td width="30%" align="center"><b>Vendor ID</b></td>
        <td width="7%"  align="center"><b>Doc No</b></td>
        <td width="11%" align="center"><b>Payment Term</b></td>
        <td width="8%"  align="center"><b>Bag Qty</b></td>
        <td width="8%"  align="center"><b>Wt/Bag (kg)</b></td>
        <td width="8%"  align="center"><b>Loose KG</b></td>
        <td width="8%"  align="center"><b>Qty (Qtl)</b></td>
        <td width="8%"  align="center"><b>Rate/Qtl</b></td>
        <td width="8%"  align="center"><b>Net Amt</b></td>
    </tr>';

    /* ================= SINGLE HISTORY ROW ================= */
    $html .= '<tr>
        <td width="4%"  align="center">'  . (1)                                                       . '</td>
        <td width="30%" align="center">'  . htmlspecialchars($item['VendorID'])                        . '</td>
        <td width="7%"  align="center">'  . htmlspecialchars($item['DocumentNo'])                      . '</td>
        <td width="11%" align="center">'  . htmlspecialchars($item['PaymentTerm'])                     . '</td>
        <td width="8%"  align="center">'  . number_format($item['BagQty'], 2)                         . '</td>
        <td width="8%"  align="center">'  . number_format($item['WeightPerBag'], 2)                   . '</td>
        <td width="8%"  align="center">'  . number_format($item['LooseKG'], 2)                        . '</td>
        <td width="8%"  align="center">'  . number_format($item['QtyQuintal'], 2)                     . '</td>
        <td width="8%"  align="right">'   . number_format($item['RatePerQuintal'], 2)                 . '</td>
        <td width="8%"  align="right">'   . number_format($item['NetAmt'], 2)                         . '</td>
    </tr>';

    /* ================= SUMMARY ================= */
    $html .= '<tr>
        <td colspan="10" style="padding:0;">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <!-- LEFT SIDE: Table format summary -->
                    <td width="70%" valign="top" style="padding:0;">
                        <table border="1" cellpadding="4" cellspacing="0" width="100%" style="font-size:11px; border-collapse:collapse;">
                            <tr style="background-color:#f2f2f2;">
                                <td align="center"><b>Bag Qty</b></td>
                                <td align="center"><b>Qty (Qtl)</b></td>
                                <td align="center"><b>Value</b></td>
                                <td align="center"><b>Brokerage</b></td>
                                <td align="center"><b>Market Levy</b></td>
                                <td align="center"><b>Round Off</b></td>
                            </tr>
                            <tr>
                                <td align="right">' . number_format($item['BagQty'], 2)     . '</td>
                                <td align="right">' . number_format($item['QtyQuintal'], 2) . '</td>
                                <td align="right">' . number_format($item['Value'], 2)      . '</td>
                                <td align="right">' . number_format($item['Brokerage'], 2)  . '</td>
                                <td align="right">' . number_format($item['MarketLevy'], 2) . '</td>
                                <td align="right">' . number_format($item['RoundOff'], 2)   . '</td>
                            </tr>
                        </table>
                    </td>

                    <!-- RIGHT SIDE: Gross / TDS / Final -->
                    <td width="30%" valign="top" style="padding:0;">
                        <table border="1" cellpadding="5" cellspacing="0" width="100%" style="font-size:11px; border-collapse:collapse;">
                            <tr>
                                <td align="right">Gross Amount</td>
                                <td align="right">' . number_format($item['Gross'], 2)         . '</td>
                            </tr>
                            <tr>
                                <td align="right">TDS Amount</td>
                                <td align="right">' . number_format($item['HistoryTDSAmt'], 2) . '</td>
                            </tr>
                            <tr>
                                <td align="right"><b>Final Amount</b></td>
                                <td align="right"><b>' . number_format($item['NetAmt'], 2) . '</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>';

    /* ================= FOOTER ================= */
    $html .= '<tr>
        <td colspan="5" style="height:30px; vertical-align:bottom;"><b>Prepared By :</b><br></td>
        <td colspan="5" style="height:30px; vertical-align:bottom;"><b>Authorized Sign :</b><br></td>
    </tr>';

    $html .= '</tbody></table>';

    $pdf->writeHTML($html, true, false, false, false, '');

} // end foreach