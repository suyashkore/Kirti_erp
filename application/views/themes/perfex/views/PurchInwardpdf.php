<?php
defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();
$pdf->SetMargins(5, 15, 5, 0);
$pdf->Ln(0);

$inv     = $invoice['invoice'];   // stdClass object
$history = $inv->history;   // Array of items

$PlantDetail = GetPlantDetails($inv->InwardsID, $inv->FY);
// echo '<pre>'; print_r($invoice); echo '</pre>'; exit();

$html = '<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:11px;">';

// ===== HEADER =====
$html .= '<thead>';
$html .= '<tr>
    <td colspan="12" align="center" style="font-size:14px;">
        <b>'.$PlantDetail->company_name.'</b><br>'.$PlantDetail->address.'
    </td>
</tr>';
$html .= '<tr>
    <td colspan="12" align="center" style="font-size:13px;"><b>Purchase Inwards</b></td>
</tr>';
$html .= '</thead>';

$html .= '<tbody>';

// ===== ROW 1: Center & PO Info =====
$html .= '<tr>
    <td colspan="3"><b>Inward No : </b>'.$inv->InwardsID.'</td>
    <td colspan="3"><b>Inward Date : </b>'.date('d/m/Y', strtotime($inv->TransDate)).'</td>
    <td colspan="3"><b>Purchase Location : </b>'.$inv->LocationName.'</td>
    <td colspan="3"><b>Vendor Location : </b>'.$inv->city_name.'</td>
</tr>';

$html .= '<tr>
    <td colspan="3"><b>Order No : </b>'.$inv->OrderID.'</td>
    <td colspan="3"><b>Order Date : </b>'.date('d/m/Y', strtotime($inv->PODate)) .'</td>
    <td colspan="3"><b>Gate In No : </b>'.$inv->gatein_no.'</td>
    <td colspan="3"><b>Vehicle No : </b>'.$inv->VehicleNo.'</td>
</tr>';
// ===== ROW 2: Vendor Info =====
$html .= '<tr>
							<td colspan="6"><b>Vendor Name : </b>'.$inv->company.'</td>
							<td colspan="6"><b>Godown Name : </b>'.$inv->GodownName.'</td>
					</tr>
					<tr>
							<td colspan="3"><b>GST : </b>'.$inv->GSTIN.'</td>
							<td colspan="9"><b>Address : </b>'.$inv->billing_address.'</td>
					</tr>';

// ===== Item Table Header =====
// ===== Item Table Header =====
$html .= '<tr style="background-color:#f2f2f2;">
    <td align="center" colspan="1"><b>Sr.</b></td>
    <td align="center" colspan="2"><b>Item ID</b></td>
    <td align="center" colspan="3"><b>Item Name</b></td>
    <td align="center" colspan="1"><b>UOM</b></td>
    <td align="center" colspan="1"><b>Pack Qty</b></td>
    <td align="center" colspan="2"><b>Unit Wt (kg)</b></td>
    <td align="center" colspan="2"><b>Unit Qty</b></td>
</tr>';

// ===== Item Rows from history =====
if (!empty($history)) {
    $sn = 1;
    foreach ($history as $item) {
        $html .= '<tr style="height:25px;">
            <td align="center" colspan="1">'.$sn++.'</td>
            <td align="center" colspan="2">'.htmlspecialchars($item->ItemID).'</td>
            <td colspan="3">'.htmlspecialchars($item->item_name).'</td>
            <td align="center" colspan="1">'.htmlspecialchars($item->SuppliedIn).'</td>
            <td align="center" colspan="1">'.number_format($item->CaseQty, 2).'</td>
            <td align="center" colspan="2">'.number_format($item->UnitWeight, 2).'</td>
            <td align="center" colspan="2">'.number_format($item->OrderQty, 2).'</td>
        </tr>';
    }
} else {
    $html .= '<tr>
        <td style="height:25px;">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        <td>&nbsp;</td><td>&nbsp;</td>
    </tr>';
}

// ===== Summary =====
$summary = [
	'Total Wt (Kg):'=> number_format($inv->TotalWeight, 2),
	'Total Qty'     => number_format($inv->TotalQuantity, 2),
	// 'Item Total'    => number_format($inv->ItemAmt, 2),
	// 'Total Disc'    => number_format($inv->ItemAmt, 2),
	// 'Taxable Amt'   => number_format($inv->TaxableAmt, 2),
	// 'CGST Amt'      => number_format($inv->CGSTAmt, 2),
	// 'SGST Amt'      => number_format($inv->SGSTAmt, 2),
	// 'IGST Amt'      => number_format($inv->IGSTAmt, 2),
	// 'Net Amt'       => number_format($inv->NetAmt, 2),
];

$start = 0;
foreach ($summary as $label => $value) {
    if($start == 0){
        $html .= '<tr>
        <td colspan="8" rowspan="2"></td>';
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
    <td colspan="6" style="height: 100px;"><b>Prepared By :</b></td>
    <td colspan="6" align="right"><b>Authorized Sign :</b></td>
</tr>';

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');