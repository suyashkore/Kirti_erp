<?php



// defined('BASEPATH') or exit('No direct script access allowed');





// $dimensions = $pdf->getPageDimensions();



// $pdf->SetMargins(5, 15, 5, 0);

// $pdf->Ln(0);

// $PlantDetail = GetPlantDetails($invoice->PlantID,$invoice->FY);

// $html = '<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:12px;">';

// $html .= '<thead>';

// $html .= '<tr><td style="text-alig:center;">'.$PlantDetail->company_name.' <br>'.$PlantDetail->address.'</td></tr>';

// $html .= '</thead>';

// $html .= '<tbody>';

// $html .= '<tr><td>Hello body</td></tr>';

// $html .= '</tbody>';

// $html .= '</table>';

/*if($invoice->PartyLedgerName){

    $PartyName = $invoice->PartyName . " (".$invoice->PartyLedgerName.")";

}else{

    $PartyName = $invoice->PartyName;

}

if($invoice->BrokerLedgerName){

    $BrokerName = $invoice->BrokerName . " (".$invoice->BrokerLedgerName.")";

}else{

    $BrokerName = $invoice->BrokerName;

}

    $html = '

<table width="100%" border="1" cellpadding="4" cellspacing="0" style="font-size:12px;">



<!-- ROW 1 -->

<tr>

    <td colspan="8">Party Name : '.$PartyName.'</td>

    <td colspan="4">Date : '._d(substr($invoice->TransDate,0,10)).'</td>

</tr>

<tr>

    <td colspan="8">Broker Name : '.$BrokerName.'</td>

    <td colspan="4">Truck No : '.$invoice->VehicleNo.'</td>

</tr>



<!-- ROW 2 -->

<tr>

    <td colspan="3">Purchase ID : '.$invoice->OrderID.'</td>

    <td colspan="3">Sauda Date : '._d(substr($invoice->SaudaDate,0,10)).'</td>

    <td colspan="3">Sauda Rate : '.$invoice->GrossRate.'</td>

    <td colspan="3">Rate Type : '.$invoice->rate_type.'</td>

</tr>



<!-- ROW 3 (Weightment Header aligned right) -->

<tr>

    <td colspan="3">Mandi GP No : '.$invoice->MandiGPNo.'</td>

    <td colspan="3">Bill No : '.$invoice->PartyBillNo.'</td>

    <td colspan="2">Dated : '._d(substr($invoice->PartyBillDate,0,10)).'</td>

    <td colspan="3" align="center"><b>Weightment Chart/Quantity</b></td>

    <td align="center"><b>Sign</b></td>

</tr>



<!-- ROW 4 -->

<tr>

    <td colspan="3">Mandi 9R No : '.$invoice->Mandi9RNo.'</td>

    <td colspan="5"></td>

    <td colspan="2">Bardana</td>

    <td align="right">'.$invoice->Bardana.'</td>

    <td rowspan="10"></td>

</tr>



<!-- ROW 5 -->

<tr>

    <td align="center">WT(qtls)</td>

    <td colspan="2" align="center">Sauda Rate</td>

    <td colspan="2" align="center">Taxable Amt</td>

    <td align="center">GST Amt</td>

    <td colspan="2" align="center">Total Amt</td>

    <td colspan="2">Gross Wt</td>

    <td align="right">'.$invoice->LoadingWeight.'</td>

    

</tr>';



//ROW 6 (Rate header row)

if($invoice->LoadingWeight > 0 && $invoice->EmptyWeight > 0){

    $NetWeight = $invoice->LoadingWeight - $invoice->EmptyWeight;

}else{

    $NetWeight = 0;

}

$TaxableAmt = $invoice->NetRate * $NetWeight;

$GSTPer = $invoice->CGST + $invoice->SGST + $invoice->IGST;

if($GSTPer > 0){

    $GSTAmt = $TaxableAmt * ($GSTPer / 100);

}else{

    $GSTAmt = 0;

}

$NetAmt = $TaxableAmt + $GSTAmt;



$html .= '<tr>

    <td align="right">'.number_format($NetWeight, 2, '.', '').'</td>

    <td colspan="2" align="right">'.$invoice->NetRate.'</td>

    <td colspan="2" align="right">'.number_format($TaxableAmt, 2, '.', '').'</td>

    <td align="right">'.number_format($GSTAmt, 2, '.', '').'</td>

    <td colspan="2" align="right">'.number_format($NetAmt, 2, '.', '').'</td>

    <td colspan="2">Tare Wt</td>

    <td align="right">'.$invoice->EmptyWeight.'</td>

</tr>



<!-- ROW 7 -->

<tr>

    <td colspan="4"><b>Cashier Sign : </b></td>

    <td colspan="4" align="center"><b>Deductions</b></td>

    <td colspan="2">Net Wt With Bags</td>

    <td align="right">'.number_format($NetWeight, 2, '.', '').'</td>

    

    

</tr>';



// ROW 8 

if($invoice->Bardana > 0){

    $BardanaWt = $invoice->Bardana/100; // in quintal

}else{

    $BardanaWt = 0;

}

if($invoice->PlBardana > 0){

    $plasticBagWt = ($invoice->PlBardana * 400)/100000; // in quintal

}else{

    $plasticBagWt = 0;

}

$BagWeight = $BardanaWt + $plasticBagWt;

$LessBagWt = $NetWeight - $BagWeight;

$html .= '<tr>

    <td rowspan="2" colspan="2"><b>Bardana Sale</b></td>

    <td>Pieces</td>

    <td>Rate</td>

    <td colspan="2">Advance</td>

    <td colspan="2" align="right"></td>

	<td colspan="2">Less Bag Wt</td>

    <td align="right">'.number_format($BagWeight, 2, '.', '').'</td>

    

</tr>



<!-- ROW 9 -->

<tr>

    <td>0</td>

    <td>0</td>

    <td colspan="2">Bardana</td>

    <td colspan="2" align="right"></td>

    <td colspan="2">Goods Wt</td>

    <td align="right">'.number_format($LessBagWt, 2, '.', '').'</td>

</tr>



<!-- ROW 10 -->

<tr>

    <td rowspan="8" colspan="4">

        <span style="font-size:19px !important;"><input type="checkbox" name="old_bardana" value="yes" /></span>Old/Phata Bardana<br>

        <span style="font-size:19px !important;"><input type="checkbox" name="dust_guna" value="yes"></span>Dust/Guna<br>

        <span style="font-size:19px !important;"><input type="checkbox" name="jow_mixing" value="yes"></span>Jow/Mixing<br>

        <span style="font-size:19px !important;"><input type="checkbox" name="baharan" value="yes"></span>Baharan<br>

        <span style="font-size:19px !important;"><input type="checkbox" name="chota_dana" value="yes"></span>Gala/Chota Dana<br>

        <span style="font-size:19px !important;"><input type="checkbox" name="plastic_bardana" value="yes"></span>Plastic Bardana

    </td>

    <td colspan="2">Cash Sale</td>

    <td colspan="2" align="right"></td>

    <td colspan="2">Less Excess Wt</td>

    <td align="right">0</td>

</tr>';



//ROW 11

$DustBag = $invoice->Dust;

$dustPerBag = $invoice->Dust_kg;

if($DustBag > 0 && $dustPerBag > 0){

    $DustDunna = ($DustBag * $dustPerBag)/100;

}else{

    $DustDunna = 0;

}

$PayableWT = $LessBagWt - $DustDunna;

$html .= '<tr>

    <td colspan="2">Claims</td>

    <td colspan="2" align="right"></td>

    <td colspan="2">Dust/Guna</td>

    <td align="right">'.number_format($DustDunna, 2, '.', '').'</td>

</tr>

<tr>

    <td colspan="2">Total Claims</td>

    <td colspan="2" align="right"></td>

    <td colspan="2">Moisture</td>

    <td align="right">'.number_format($invoice->Moisture, 2, '.', '').'</td>

</tr>



<!-- ROW 12 -->

<tr>

    <td colspan="2"></td>

    <td colspan="2" align="right"></td>

    <td colspan="2">Payable Wt.</td>

    <td align="right"><b>'.number_format($PayableWT, 2, '.', '').'</b></td>

</tr>



<!-- ROW 13 -->

<tr>

    <td colspan="4" rowspan="4"></td>

    <td colspan="3" align="center"><b>Claims & Deductions</b></td>

    <td align="center"><b>Amount</b></td>

</tr>



<!-- ROW 14 -->

<tr>

    <td colspan="2">Discount</td>

    <td align="right"></td>

    <td align="right"></td>

</tr>



<!-- ROW 15 -->

<tr>

    <td colspan="2">Dalali</td>

    <td align="right"></td>

    <td align="right"></td>

    

</tr>



<!-- ROW 16 -->

<tr>

    <td colspan="2">Bardana</td>

    <td align="right"></td>

    <td align="right"></td>

    

</tr>





<!-- ROW 17 -->



<tr>

	<td colspan="8">Driver\'s Sign :</td>

    <td colspan="2">Unloading</td>

    <td align="right"></td>

    <td align="right"></td>

</tr>



</table>

';*/



    // $pdf->writeHTML($html, true, false, false, false, '');



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
    <td colspan="10" align="center" style="font-size:14px;">
        <b>' . $PlantDetail->company_name . '</b><br>' . $PlantDetail->address . '
    </td>
</tr>';
$html .= '<tr>
    <td colspan="10" align="center" style="font-size:13px;"><b>Purchase Inwards</b></td>
</tr>';
$html .= '</thead>';

$html .= '<tbody>';

// ===== ROW 1: Center & PO Info =====
$html .= '<tr>
    <td colspan="3"><b>Item/Service : </b>' . $inv->ItemTypeName . '</td>
    <td colspan="3"><b>Purchase Location : </b>' . $inv->LocationName . '</td>
    <td colspan="2"><b>Inward No : </b>' . $inv->InwardsID . '</td>
    <td colspan="2"><b>Inward Date : </b>' . date('d/m/Y', strtotime($inv->TransDate)) . '</td>
</tr>';
$html .= '<tr>
    <td colspan="3"><b>Order Category : </b>' . $inv->CategoryName . '</td>
    <td colspan="3"><b>Vendor Location : </b>' . $inv->city_name . '</td>
    <td colspan="2"><b>Payment Terms : </b>' . $inv->PaymentTerms . '</td>
    <td colspan="2"><b>Freight Terms : </b>' . $inv->FreightTerms . '</td>
</tr>';

$html .= '<tr>
    <td colspan="3"><b>Order No : </b>'.$inv->OrderID.'</td>
    <td colspan="3"><b>Order Date : </b>'.date('d/m/Y', strtotime($inv->PODate)) .'</td>
    <td colspan="2"><b>Delivery From : </b>' . date('d/m/Y', strtotime($inv->DeliveryFrom)) . '</td>
    <td colspan="2"><b>Delivery To : </b>' . date('d/m/Y', strtotime($inv->DeliveryTo)) . '</td>
</tr>';
// ===== ROW 2: Vendor Info =====
$html .= '<tr>
    <td colspan="4"><b>Vendor Name : </b>' . $inv->company . '</td>
    <td colspan="2"><b>GST : </b>' . $inv->GSTIN . '</td>
    <td colspan="4"><b>Address : </b>' . $inv->billing_address . '</td>
</tr>';

// ===== Item Table Header =====
// ===== Item Table Header =====
$html .= '<tr style="background-color:#f2f2f2;">
    <td align="center"><b>Sr.</b></td>
    <td align="center"><b>Item ID</b></td>
    <td align="center"><b>UOM</b></td>
    <td align="center"><b>Pack Qty</b></td>
    <td align="center"><b>Pack Wt (kg)</b></td>
    <td align="center"><b>Purch Unit Qty</b></td>
    <td align="center"><b>Basic Rate</b></td>
    <td align="center"><b>Disc Amt</b></td>
    <td align="center"><b>GST %</b></td>
    <td align="center"><b>Net Amount</b></td>
</tr>';

// ===== Item Rows from history =====
if (!empty($history)) {
    $sn = 1;
    foreach ($history as $item) {

        $cgst = floatval($item->cgst);
        $sgst = floatval($item->sgst);
        $igst = floatval($item->igst);

        if ($cgst > 0 || $sgst > 0) {
            // CGST + SGST दोन्ही असतील तर plus करून दाखवा
            $total_gst = $cgst + $sgst;
            $gst_label = $total_gst . '%';
        } elseif ($igst > 0) {
            // दोन्ही 0 असतील तर IGST दाखवा
            $gst_label = $igst . '%';
        } else {
            $gst_label = '0%';
        }

        $html .= '<tr style="height:25px;">
            <td align="center">' . $sn++ . '</td>
            <td>' . htmlspecialchars($item->ItemID) . '</td>
            <td align="center">' . htmlspecialchars($item->SuppliedIn) . '</td>
            <td align="center">' . number_format($item->CaseQty, 2) . '</td>
            <td align="center">' . number_format($item->UnitWeight, 2) . '</td>
            <td align="center">' . number_format($item->OrderQty, 2) . '</td>
            <td align="right">' . number_format($item->BasicRate, 2) . '</td>
            <td align="right">' . number_format($item->DiscAmt, 2) . '</td>
            <td align="center">' . $gst_label . '</td>
            <td align="right">' . number_format($item->NetOrderAmt, 2) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr>
        <td style="height:25px;">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
        <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>';
}

// ===== Summary =====
$summary = [
    'Total Wt (Kg):'=> number_format($inv->TotalWeight, 2),
    'Total Qty'     => number_format($inv->TotalQuantity, 2),
    'Item Total'    => number_format($inv->ItemAmt, 2),
    'Total Disc'    => number_format($inv->ItemAmt, 2),
    'Taxable Amt'   => number_format($inv->TaxableAmt, 2),
    'CGST Amt'      => number_format($inv->CGSTAmt, 2),
    'SGST Amt'      => number_format($inv->SGSTAmt, 2),
    'IGST Amt'      => number_format($inv->IGSTAmt, 2),
    'Net Amt'       => number_format($inv->NetAmt, 2),
];

$start = 0;
foreach ($summary as $label => $value) {
    if($start == 0){
        $html .= '<tr>
        <td colspan="8" rowspan="9"></td>';
        $start = 1;
    }else{
        $html .= '<tr>';
    }
    $html .= '<td align="right"><b>' . $label . '</b></td>
        <td align="right"><b>' . $value . '</b></td>
    </tr>';
}

// ===== Footer =====
$html .= '<tr>
    <td colspan="5" style="height: 80px;"><b>Prepared By :</b></td>
    <td colspan="5" align="right"><b>Authorized Sign :</b></td>
</tr>';

$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');