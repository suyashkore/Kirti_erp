<?php
defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();
$pdf->SetMargins(5, 15, 5, 0);
$pdf->Ln(0);

$inv     = $invoice['invoice'];   // stdClass object
$history = $inv->history;   // Array of items

$PlantDetail = GetPlantDetails($inv->OrderID, $inv->FY);
// echo '<pre>'; print_r($invoice); echo '</pre>'; exit();

$html = '<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:11px;">';

// ===== HEADER =====
$html .= '<thead>';
$html .= '<tr>
    <td colspan="17" align="center" style="font-size:14px;">
        <b>'.$PlantDetail->company_name.'</b><br>'.$PlantDetail->address.'
    </td>
</tr>';
$html .= '<tr>
    <td colspan="17" align="center" style="font-size:13px;"><b>Direct Purchase Order</b></td>
</tr>';
$html .= '</thead>';

$html .= '<tbody>';

// ===== ROW 1: Center & PO Info =====
$html .= '<tr>
    <td colspan="5"><b>Order No : </b>'.$inv->OrderID.'</td>
    <td colspan="6"><b>Order Date : </b>'.date('d/m/Y', strtotime($inv->OrderDate)).'</td>
    <td colspan="6"><b>Center Location : </b>'.$inv->LocationName.'</td>
</tr>';

$html .= '<tr>
    <td colspan="5"><b>Item/Service :</b> '.$inv->ItemTypeName.'</td>
    <td colspan="6"><b>TDS Section :</b> '.$inv->TDSName.'</td>
    <td colspan="6"><b>TDS Rate :</b> '.$inv->TDSRate.' %</td>
</tr>';

// ===== ROW 2: Vendor Info =====
$html .= '<tr>
    <td colspan="10"><b>Vendor Name : </b>'.$inv->VendorName.'</td>
    <td colspan="7"><b>Godown Name : </b>'.$inv->GodownName.'</td>
</tr>
<tr>
    <td colspan="5"><b>GST : </b>'.$inv->GSTIN.'</td>
    <td colspan="12"><b>Address : </b>'.$inv->billing_address.'</td>
</tr>';

// ===== Item Table Header =====
// ===== Item Table Header =====
$html .= '<tr style="background-color:#f2f2f2;">
    <td align="center"><b>Sr.</b></td>
    <td align="center"><b>Item ID</b></td>
    <td align="center" colspan="2"><b>Item Name</b></td>
    <td align="center" colspan="2"><b>Group</b></td>
    <td align="center"><b>HSN</b></td>
    <td align="center"><b>UOM</b></td>
    <td align="center"><b>Qty</b></td>
    <td align="center"><b>Wt (kg)</b></td>
    <td align="center"><b>Basic Rate</b></td>
    <td align="center"><b>Disc Amt</b></td>
    <td align="center"><b>GST %</b></td>
    <td align="center"><b>CGST Amt</b></td>
    <td align="center"><b>SGST Amt</b></td>
    <td align="center"><b>IGST Amt</b></td>
    <td align="center"><b>Net Amt</b></td>
</tr>';

// ===== Item Rows from history =====
if (!empty($history)) {
    $sn = 1;
    foreach ($history as $item) {
        $cgst = floatval($item->cgst);
        $sgst = floatval($item->sgst);
        $igst = floatval($item->igst);

        if ($cgst > 0 || $sgst > 0) {
            $gst_label = ($cgst + $sgst).'%';
        } elseif ($igst > 0) {
            $gst_label = $igst.'%';
        } else {
            $gst_label = '0%';
        }

        $html .= '<tr>
            <td align="center">'.$sn++.'</td>
            <td align="center">'.htmlspecialchars($item->ItemID).'</td>
            <td align="left" colspan="2">'.htmlspecialchars($item->item_name).'</td>
            <td align="left" colspan="2">'.htmlspecialchars($item->DivisionName).'</td>
            <td align="center">'.htmlspecialchars($item->hsn_code).'</td>
            <td align="center">'.htmlspecialchars($item->SuppliedIn).'</td>
            <td align="center">'.round($item->OrderQty).'</td>
            <td align="center">'.number_format($item->UnitWeight, 2).'</td>
            <td align="right">'.number_format($item->BasicRate, 2).'</td>
            <td align="right">'.number_format($item->DiscAmt, 2).'</td>
            <td align="center">'.$gst_label.'</td>
            <td align="center">'.number_format($item->cgstamt, 2).'</td>
            <td align="center">'.number_format($item->sgstamt, 2).'</td>
            <td align="center">'.number_format($item->igstamt, 2).'</td>
            <td align="right">'.round($item->NetOrderAmt).'</td>
        </tr>';
    }
} else {
    $html .= '<tr>
        <td style="height:25px;">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>';
}

// ===== Summary =====
$summary = [
	'Total Amt'     => number_format($inv->PurchaseAmt, 2),
	'Total Disc'    => number_format($inv->DiscAmt, 2),
	'CGST Amt'      => number_format($inv->CGSTAmt, 2),
	'SGST Amt'      => number_format($inv->SGSTAmt, 2),
	'IGST Amt'      => number_format($inv->IGSTAmt, 2),
	'Freight Amt'   => number_format($inv->FreightAmt, 2),
	'Other Amt'     => number_format($inv->OtherAmt, 2),
	'Round Off'     => number_format($inv->RoundOff, 2),
	'TDS Amt'       => number_format($inv->TDSAmt, 2),
	'Final Amt'     => number_format($inv->FinalAmt, 2),
];

$start = 0;
foreach ($summary as $label => $value) {
    if($start == 0){
        $html .= '<tr>
        <td colspan="13" rowspan="10"></td>';
        $start = 1;
    }else{
        $html .= '<tr>';
    }
    $html .= '<td align="right" colspan="2"><b>'.$label.'</b></td>
        <td align="right" colspan="2"><b>'.$value.'</b></td>
    </tr>';
}

// ===== Footer =====
$html .= '<tr>
    <td colspan="8" style="height: 100px;"><b>Prepared By :</b></td>
    <td colspan="9" align="right"><b>Authorized Sign :</b></td>
</tr>';

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');