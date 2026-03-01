<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();
$pdf->SetMargins(5, 15, 5, 0);
$pdf->Ln(0);

$inv = $invoice['invoice'];
$history = $invoice['history'];

$PlantDetail = GetPlantDetails($inv->PurchID, $inv->FY);

$html = '<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:11px;">';

/* ================= HEADER ================= */

$html .= '<thead>
<tr>
    <td colspan="10" align="center" style="font-size:14px;">
        <b>' . $PlantDetail->company_name . '<br>' . $PlantDetail->address . '<br>GSTIN: ' . $PlantDetail->gst . ' | Contact: ' . $PlantDetail->mobile1 . '<br>Email: ' . $PlantDetail->BusinessEmail . '</b>
    </td>
</tr>
<tr>
    <td colspan="10" align="center" style="font-size:13px;"><b>Purchase Order</b></td>
</tr>
</thead>';

$html .= '<tbody>';

/* ================= BASIC INFO ================= */

$html .= '<tr>
    <td colspan="2"><b>PO No :</b> ' . $inv->PurchID . '</td>
    <td colspan="2"><b>PO Date :</b> ' . date('d-m-Y', strtotime($inv->TransDate)) . '</td>
    <td colspan="3"><b>Item/Service :</b> ' . $inv->ItemTypeName . '</td>
    <td colspan="3"><b>Center State :</b> ' . $inv->state_name . '</td>
</tr>';

$html .= '<tr>
    <td colspan="2"><b>Order Category :</b> ' . $inv->CategoryName . '</td>
    <td colspan="2"><b>Payment Terms :</b> ' . $inv->PaymentTerms . '</td>
    <td colspan="3"><b>Purchase Location :</b> ' . $inv->LocationName . '</td>
    <td colspan="3"><b>Freight Terms :</b> ' . $inv->FreightTerms . '</td>
</tr>';

$html .= '<tr>
    <td colspan="2"><b>Quotation No :</b> ' . $inv->QuatationID . '</td>
    <td colspan="2"><b>Quotation Date :</b> ' . date('d-m-Y', strtotime($inv->QuotationDate)) . '</td>
    <td colspan="3"><b>Delivery From :</b> ' . date('d-m-Y', strtotime($inv->DeliveryFrom)) . '</td>
    <td colspan="3"><b>Delivery To :</b> ' . date('d-m-Y', strtotime($inv->DeliveryTo)) . '</td>
</tr>';

$html .= '<tr>
    <td colspan="2"><b>GST :</b> ' . $inv->GSTIN . '</td>
    <td colspan="2"><b>Vendor Location :</b> ' . $inv->ShippingCityName . '</td>
    <td colspan="3"><b>Vendor Doc No :</b> ' . $inv->VendorDocNo . '</td>
    <td colspan="3"><b>Vendor Doc Date :</b> ' . date('d-m-Y', strtotime($inv->VendorDocDate)) . '</td>
</tr>';

$html .= '<tr>
    <td colspan="5"><b>Vendor Name :</b> ' . $inv->company . '</td>
    <td colspan="5"><b>Address :</b> ' . $inv->billing_address . '</td>
</tr>';

/* ================= ITEM HEADER ================= */

$html .= '<tr style="background-color:#f2f2f2;">
    <td align="center" style="width:25px;"><b>Sr.</b></td>
    <td align="center" style="width:250px;"><b>Item Name</b></td>
    <td align="center" style="width:70px;"><b>UOM</b></td>
    <td align="center" style="width:70px;"><b>Pack Qty</b></td>
    <td align="center" style="width:80px;"><b>Pack Wt (kg)</b></td>
    <td align="center" style="width:92px;"><b>Purch Unit Qty</b></td>
    <td align="center" style="width:70px;"><b>Basic Rate</b></td>
    <td align="center" style="width:70px;"><b>Disc Amt</b></td>
    <td align="center" style="width:70px;"><b>GST %</b></td>
    <td align="center" style="width:70px;"><b>Net Amount</b></td>
</tr>';

/* ================= ITEM ROWS ================= */

if (!empty($history)) {
    foreach ($history as $item) {

        $cgst = floatval($item['cgst']);
        $sgst = floatval($item['sgst']);
        $igst = floatval($item['igst']);

        if ($cgst > 0 || $sgst > 0) {
            $gst_label = ($cgst + $sgst) . '%';
        } elseif ($igst > 0) {
            $gst_label = $igst . '%';
        } else {
            $gst_label = '0%';
        }

        $html .= '<tr>
            <td align="center">' . $item['Ordinalno'] . '</td>
            <td>' . htmlspecialchars($item['ItemName']) . '</td>
            <td align="center">' . htmlspecialchars($item['SuppliedIn']) . '</td>
            <td align="center">' . number_format($item['CaseQty'], 2) . '</td>
            <td align="center">' . number_format($item['UnitWeight'], 2) . '</td>
            <td align="center">' . number_format($item['OrderQty'], 2) . '</td>
            <td align="right">' . number_format($item['BasicRate'], 2) . '</td>
            <td align="right">' . number_format($item['DiscAmt'], 2) . '</td>
            <td align="center">' . $gst_label . '</td>
            <td align="right">' . number_format($item['NetOrderAmt'], 2) . '</td>
        </tr>';
    }
}

/* ================= SUMMARY SECTION (NO EMPTY SPACE) ================= */

$html .= '<tr>
    <td colspan="10" style="padding:0;">
        <table width="100%" border="0" cellpadding="4" cellspacing="0">
            <tr>
                <!-- LEFT SIDE -->
                <td width="65%" align="left">
                    <b>Total Wt (Kg):</b> ' . number_format($inv->TotalWeight, 2) . '
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <b>Total Qty:</b> ' . number_format($inv->TotalQuantity, 2) . '
                </td>

                <!-- RIGHT SIDE SUMMARY BOX -->
                <td width="35%" align="right">
                    <table border="1" cellpadding="5" cellspacing="0" width="100%" style="font-size:11px;">
                        <tr>
                            <td align="right">Item Total</td>
                            <td align="right">' . number_format($inv->ItemAmt, 2) . '</td>
                        </tr>
                        <tr>
                            <td align="right">Taxable Amt</td>
                            <td align="right">' . number_format($inv->TaxableAmt, 2) . '</td>
                        </tr>
                        <tr>
                            <td align="right">CGST Amt</td>
                            <td align="right">' . number_format($inv->CGSTAmt, 2) . '</td>
                        </tr>
                        <tr>
                            <td align="right">SGST Amt</td>
                            <td align="right">' . number_format($inv->SGSTAmt, 2) . '</td>
                        </tr>
                        <tr>
                            <td align="right">IGST Amt</td>
                            <td align="right">' . number_format($inv->IGSTAmt, 2) . '</td>
                        </tr>
                        <tr>
                            <td align="right"><b>Net Amt</b></td>
                            <td align="right"><b>' . number_format($inv->NetAmt, 2) . '</b></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>';


/* ================= FOOTER ================= */

$html .= '<tr style="height:50px;">
    <td colspan="5"><b>Prepared By :</b></td>
    <td colspan="5"><b>Authorized Sign :</b></td>
</tr>';

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, false, false, '');