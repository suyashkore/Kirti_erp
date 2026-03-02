<?php
defined('BASEPATH') or exit('No direct script access allowed');

$PlantDetail = GetPlantDetails($invoice);
// echo"";print_r($PlantDetail);exit;

// =============================================
// DATA EXTRACT
// =============================================
$inv      = isset($invoice['invoice']) ? $invoice['invoice'] : [];

$gatein      = isset($inv['gatein'])      ? $inv['gatein']      : new stdClass();
$inward      = isset($inv['inward'])      ? $inv['inward']      : [];
$order       = isset($inv['order'])       ? $inv['order']       : [];
$history     = isset($inward['history'][0]) ? $inward['history'][0] : [];
$gross_weight = isset($inv['gross_weight']) ? $inv['gross_weight'] : new stdClass();
$tare_weight  = isset($inv['tare_weight'])  ? $inv['tare_weight']  : new stdClass();
$gate_out     = isset($inv['gate_out'])     ? $inv['gate_out']     : new stdClass();

// =============================================
// GATEIN (object ->)
// =============================================
$gateInNo     = isset($inward['gatein_no'])    ? $inward['gatein_no']                                    : 'N/A';
$gateInTime   = isset($gatein->TransDate)      ? date('d-m-Y H:i', strtotime($gatein->TransDate))        : 'N/A';
$asnDate      = (isset($gatein->ASNDate) && $gatein->ASNDate != '') 
                    ? date('d-m-Y H:i', strtotime($gatein->ASNDate)) 
                    : '-';
$location     = isset($gatein->LocationName)   ? $gatein->LocationName                                   : 'N/A';
$vehicleNo    = isset($gatein->VehicleNo)      ? $gatein->VehicleNo                                      : 'N/A';
$driverName   = isset($gatein->DriverName)     ? $gatein->DriverName                                     : '-';
$driverMobile = isset($gatein->DriverMobileNo) ? $gatein->DriverMobileNo                                 : '-';

// =============================================
// INWARD (array [])
// =============================================
$bookingID    = isset($inward['InwardsID'])     ? $inward['InwardsID']                                   : 'N/A';
$purchaseID   = isset($inward['OrderID'])       ? $inward['OrderID']                                     : 'N/A';
$bookingDate  = isset($inward['TransDate'])     ? date('d-m-Y', strtotime($inward['TransDate']))         : 'N/A';
$gateOutTime  = isset($gate_out->value->Time)   ? date('d-m-Y H:i', strtotime($gate_out->value->Time))  : '-';
$paymentTerms = isset($inward['PaymentTerms'])  ? $inward['PaymentTerms']                                : 'N/A';
$orderID      = isset($inward['OrderID'])       ? $inward['OrderID']                                     : 'N/A';

$partyName    = isset($inward['company'])       ? $inward['company']                                     : 'N/A';
$totalWeight  = isset($inward['TotalWeight'])   ? $inward['TotalWeight']                                 : 'N/A';
$noOfBags     = isset($inward['TotalQuantity']) ? $inward['TotalQuantity']                               : 'N/A';
$netAmt       = isset($inward['NetAmt'])        ? $inward['NetAmt']                                      : 'N/A';
$taxableAmt   = isset($inward['TaxableAmt'])    ? $inward['TaxableAmt']                                  : 'N/A';

// =============================================
// ORDER (array [])
// =============================================
$vendorDocNo   = isset($order['VendorDocNo'])      ? $order['VendorDocNo']                               : '-';
$vendorDocDate = isset($order['VendorDocDate'])    ? date('d-m-Y', strtotime($order['VendorDocDate']))   : '-';
$gstin         = (isset($order['GSTIN']) && $order['GSTIN'] !== '') ? $order['GSTIN']                   : '-';
$internalRmk   = isset($order['Internal_Remarks']) ? $order['Internal_Remarks']                          : '-';
$docRemark     = isset($order['Document_Remark'])  ? $order['Document_Remark']                           : '-';

// =============================================
// HISTORY (array []) - first item
// =============================================
$commodity  = isset($history['item_name'])  ? $history['item_name']  : 'N/A';
$unit       = isset($history['SuppliedIn']) ? $history['SuppliedIn'] : 'MT';
$basicRate  = isset($history['BasicRate'])  ? $history['BasicRate']  : 'N/A';
$orderQty   = isset($history['OrderQty'])   ? $history['OrderQty']   : 'N/A';
$unitWeight = isset($history['UnitWeight']) ? $history['UnitWeight'] : 'N/A';
$igstPerc   = isset($history['igst'])       ? $history['igst']       : '0';
$igstAmtH   = isset($history['igstamt'])    ? $history['igstamt']    : '0';

// =============================================
// GROSS WEIGHT → gross_weight->value->gross_weight (sum)
// =============================================
$grossWeightVal = '-';
if (isset($gross_weight->value)) {
    $gwVal = $gross_weight->value;
    if (is_array($gwVal)) {
        $sum = 0;
        foreach ($gwVal as $gw) {
            $sum += isset($gw->gross_weight) ? floatval($gw->gross_weight) : 0;
        }
        $grossWeightVal = $sum;
    } elseif (is_object($gwVal) && isset($gwVal->gross_weight)) {
        $grossWeightVal = $gwVal->gross_weight;
    }
}

// =============================================
// TARE WEIGHT → tare_weight->value->tare_weight (sum)
// =============================================
$tareWeightVal = '-';
if (isset($tare_weight->value)) {
    $twVal = $tare_weight->value;
    if (is_array($twVal)) {
        $sum = 0;
        foreach ($twVal as $tw) {
            $sum += isset($tw->tare_weight) ? floatval($tw->tare_weight) : 0;
        }
        $tareWeightVal = $sum;
    } elseif (is_object($twVal) && isset($twVal->tare_weight)) {
        $tareWeightVal = $twVal->tare_weight;
    }
}

// NET WEIGHT = Gross - Tare
$netWeight = '-';
if ($grossWeightVal !== '-' && $tareWeightVal !== '-') {
    $netWeight = floatval($grossWeightVal) - floatval($tareWeightVal);
}

// =============================================
// TYPE CHECKBOXES — DejaVu font Unicode symbols
// ✔ = checked  ☐ = unchecked
// =============================================
$ttype = isset($history['TType']) ? strtoupper(trim($history['TType'])) : '';

// Default to Sales (S) if empty
if ($ttype === '') {
    $ttype = 'S';
}

$chkDeposit    = ($ttype == 'D') ? '&#10004;' : '&#9744;';
$chkWithdrawal = ($ttype == 'W') ? '&#10004;' : '&#9744;';
$chkSales      = ($ttype == 'S') ? '&#10004;' : '&#9744;';
$chkPurchase   = ($ttype == 'P') ? '&#10004;' : '&#9744;';
$chkAnamat     = ($ttype == 'A') ? '&#10004;' : '&#9744;';
$chkTF         = ($ttype == 'T') ? '&#10004;' : '&#9744;';

// =============================================
// PDF SETUP — DejaVu font for Unicode support
// =============================================
$pdf->SetMargins(10, 10, 10);
$pdf->SetFont('dejavusans', '', 9);

$html = '
<table border="1" cellpadding="5" cellspacing="0" width="100%">

    <!-- HEADER -->
    <tr>
        <td colspan="4" align="center" style="background-color:#d9e1f2; font-size:14pt;">
<b><span style="font-size:17pt;">' . $PlantDetail->company_name . '</span><br><span style="font-size:10pt;">' . $PlantDetail->address . '<br>GSTIN: ' . $PlantDetail->gst . ' | Contact: ' . $PlantDetail->mobile1 . '<br>Email: ' . $PlantDetail->BusinessEmail . '</span></b><br/>        </td>
    </tr>
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea; font-size:12pt;">
            <b>GATE IN PASS</b>
        </td>
    </tr>

    <!-- BASIC DETAILS -->
    <tr>
        <td width="22%" style="background-color:#f5f5f5;"><b>GateIn No</b></td>
        <td width="28%">' . $gateInNo . '</td>
        <td width="22%" style="background-color:#f5f5f5;"><b>Inward ID</b></td>
        <td width="28%">' . $bookingID . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Purchase Order</b></td>
        <td>' . $purchaseID . '</td>
        <td style="background-color:#f5f5f5;"><b>Booking Date</b></td>
        <td>' . $bookingDate . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>GateIn Time</b></td>
        <td>' . $gateInTime . '</td>
        <td style="background-color:#f5f5f5;"><b>ASN Date</b></td>
        <td>' . $asnDate . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>GateOut Time</b></td>
        <td>' . $gateOutTime . '</td>
        <td style="background-color:#f5f5f5;"><b>Location</b></td>
        <td>' . $location . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Payment Terms</b></td>
        <td>' . $paymentTerms . '</td>
        <td style="background-color:#f5f5f5;"><b>Order ID</b></td>
        <td>' . $orderID . '</td>
    </tr>

    <!-- TRANSACTION TYPE -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Transaction Type</b></td>
    </tr>
    <tr>
        <td align="center">' . $chkDeposit    . ' Deposit</td>
        <td align="center">' . $chkWithdrawal . ' Withdrawal</td>
        <td align="center">' . $chkSales      . ' Sales</td>
        <td align="center">' . $chkPurchase   . ' Purchase</td>
    </tr>
    <tr>
        <td align="center">' . $chkAnamat . ' Anamat</td>
        <td align="center">' . $chkTF     . ' T/F</td>
        <td colspan="2"></td>
    </tr>

    <!-- PARTY DETAILS -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Party Details</b></td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Party Name</b></td>
        <td colspan="3">' . $partyName . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>GSTIN</b></td>
        <td>' . $gstin . '</td>
        <td style="background-color:#f5f5f5;"><b>Vehicle No</b></td>
        <td>' . $vehicleNo . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Driver Name</b></td>
        <td>' . $driverName . '</td>
        <td style="background-color:#f5f5f5;"><b>Driver Mobile</b></td>
        <td>' . $driverMobile . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Vendor Doc No</b></td>
        <td>' . $vendorDocNo . '</td>
        <td style="background-color:#f5f5f5;"><b>Vendor Doc Date</b></td>
        <td>' . $vendorDocDate . '</td>
    </tr>

    <!-- COMMODITY DETAILS -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Commodity Details</b></td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Commodity</b></td>
        <td>' . $commodity . '</td>
        <td style="background-color:#f5f5f5;"><b>Unit</b></td>
        <td>' . $unit . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Basic Rate</b></td>
        <td>' . $basicRate . '</td>
        <td style="background-color:#f5f5f5;"><b>Unit Weight</b></td>
        <td>' . $unitWeight . ' MT</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Order Qty</b></td>
        <td>' . $orderQty . ' ' . $unit . '</td>
        <td style="background-color:#f5f5f5;"><b>No of Bags</b></td>
        <td>' . $noOfBags . '</td>
    </tr>

    <!-- WEIGHT DETAILS -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Weight Details</b></td>
    </tr>
    <tr>
        <td align="center" style="background-color:#f5f5f5;"><b>Gross Weight</b></td>
        <td align="center" style="background-color:#f5f5f5;"><b>Tare Weight</b></td>
        <td align="center" style="background-color:#f5f5f5;"><b>Net Weight</b></td>
        <td align="center" style="background-color:#f5f5f5;"><b>Unit</b></td>
    </tr>
    <tr>
        <td align="center">' . $grossWeightVal . '</td>
        <td align="center">' . $tareWeightVal  . '</td>
        <td align="center">' . $netWeight      . '</td>
        <td align="center">' . $unit           . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>IGST %</b></td>
        <td>' . $igstPerc . '%</td>
        <td style="background-color:#f5f5f5;"><b>IGST Amount</b></td>
        <td>' . $igstAmtH . '</td>
    </tr>

    <!-- AMOUNT -->
    <tr>
        <td style="background-color:#bdd0ea;"><b>Taxable Amount</b></td>
        <td style="background-color:#bdd0ea;">Rs. ' . $taxableAmt . '</td>
        <td style="background-color:#bdd0ea;"><b>Net Amount</b></td>
        <td style="background-color:#bdd0ea;"><b>Rs. ' . $netAmt . '</b></td>
    </tr>

    <!-- REMARKS -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Remarks</b></td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Internal Remark</b></td>
        <td>' . $internalRmk . '</td>
        <td style="background-color:#f5f5f5;"><b>Document Remark</b></td>
        <td>' . $docRemark . '</td>
    </tr>

    <!-- SIGNATURE -->
    <tr style="height:55px;">
        <td colspan="2" align="center"><br/><br/>____________________________<br/>Signature &amp; Name of Driver</td>
        <td colspan="2" align="center"><br/><br/>____________________________<br/>Signature of WH Manager / Supervisor</td>
    </tr>

</table>
';

$pdf->writeHTML($html, true, false, false, false, '');