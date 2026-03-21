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

    // ================= CALCULATED VALUES =================
    $brokerageTotal   = $item['Brokerage']   * $item['QtyQuintal'];
    $marketLevyTotal  = $item['MarketLevy']  * $item['QtyQuintal'];

    $html = '<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:11px;">';

    /* ================= HEADER ================= */
    $html .= '<thead>
    <tr>
        <td colspan="8" align="center" style="font-size:14px;">
            <b>' . $PlantDetail->company_name . '<br>'
                . $PlantDetail->address . '<br>
                GSTIN: ' . $PlantDetail->gst . ' | Contact: ' . $PlantDetail->mobile1 . '<br>
                Email: ' . $PlantDetail->BusinessEmail . '</b>
        </td>
    </tr>
    <tr>
        <td colspan="8" align="center" style="font-size:13px;"><b>Mandi Purchase Order</b></td>
    </tr>
    </thead>';

    $html .= '<tbody>';

    /* ================= BASIC INFO ================= */
    $html .= '<tr>
        <td colspan="2"><b>PO No :</b> '      . $inv['OrderID']                                 . '</td>
        <td colspan="2"><b>PO Date :</b> '    . date('d/m/Y', strtotime($inv['OrderDate']))     . '</td>
        <td colspan="2"><b>Vehicle No :</b> ' . $inv['VehicleNo']                               . '</td>
        <td colspan="2"><b>Trans Date :</b> ' . date('d/m/Y', strtotime($inv['TransDate']))     . '</td>
    </tr>';

    $html .= '<tr>
        <td colspan="2"><b>Warehouse :</b> '  . $inv['WarehouseID']                             . '</td>
        <td colspan="2"><b>Item Name :</b> '  . $inv['ItemID']                                  . '</td>
        <td colspan="4"><b>Center Loc :</b> ' . $inv['CenterLocation']                          . '</td>
    </tr>';

    $html .= '<tr>
        <td colspan="8"><b>Vendor Name :</b> ' . htmlspecialchars($item['VendorID']) . '</td>
    </tr>';

    /* ================= HISTORY HEADER ================= */
    $html .= '<tr style="background-color:#f2f2f2;">
        <td width="6%"  align="center"><b>Sr.</b></td>
        <td width="16%" align="center"><b>Doc No</b></td>
        <td width="11%" align="center"><b>Bag</b></td>
        <td width="11%" align="center"><b>Wt/Bag</b></td>
        <td width="11%" align="center"><b>Loose</b></td>
        <td width="11%" align="center"><b>Qty</b></td>
        <td width="11%" align="center"><b>Rate</b></td>
        <td width="23%" align="center"><b>Amount</b></td>
    </tr>';

    /* ================= SINGLE HISTORY ROW ================= */
    $html .= '<tr>
        <td width="6%"  align="center">' . (1)                                              . '</td>
        <td width="16%" align="center">' . htmlspecialchars($item['DocumentNo'])             . '</td>
        <td width="11%" align="center">' . number_format($item['BagQty'], 2)                . '</td>
        <td width="11%" align="center">' . number_format($item['WeightPerBag'], 2)          . '</td>
        <td width="11%" align="center">' . number_format($item['LooseKG'], 2)               . '</td>
        <td width="11%" align="center">' . number_format($item['QtyQuintal'], 2)            . '</td>
        <td width="11%" align="right">'  . number_format($item['RatePerQuintal'], 2)        . '</td>
        <td width="23%" align="right">'  . number_format($item['NetAmt'], 2)                . '</td>
    </tr>';

    $html .= '</tbody></table>';

    /* ================= SUMMARY ================= */
    $html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-size:11px;">
        <tr>
            <td width="55%" style="border-left:1px solid #000; border-bottom:1px solid #000;"></td>
            <td width="45%" style="padding:0;">
                <table border="1" cellpadding="5" cellspacing="0" width="100%"
                       style="font-size:11px; border-collapse:collapse;">
                    <tr>
                        <td align="right">Gross Amount</td>
                        <td align="right">' . number_format($item['Gross'], 2) . '</td>
                    </tr>
                    <tr>
                        <td align="right">Brokerage</td>
                        <td align="right">' . number_format($brokerageTotal, 2) . '</td>
                    </tr>
                    <tr>
                        <td align="right">Market Levy</td>
                        <td align="right">' . number_format($marketLevyTotal, 2) . '</td>
                    </tr>
                    <tr>
                        <td align="right"><b>Net Amount</b></td>
                        <td align="right"><b>' . number_format($item['NetAmt'], 2) . '</b></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>';

    /* ================= FOOTER ================= */
    $html .= '<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:11px;">
        <tr>
            <td width="50%" style="height:30px; vertical-align:bottom;"><b>Prepared By :</b><br></td>
            <td width="50%" style="height:30px; vertical-align:bottom;"><b>Authorized Sign :</b><br></td>
        </tr>
    </table>';

    $pdf->writeHTML($html, true, false, false, false, '');

} // end foreach