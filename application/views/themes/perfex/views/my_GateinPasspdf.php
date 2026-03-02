<?php
defined('BASEPATH') or exit('No direct script access allowed');

$PlantDetail = GetPlantDetails($invoice);
// echo"<pre>";
// print_r($invoice);die;

// =============================================
// DATA EXTRACT
// =============================================
$inv          = isset($invoice['invoice']) ? $invoice['invoice'] : [];

$gatein       = isset($inv['gatein'])       ? $inv['gatein']       : new stdClass();
$inward       = isset($inv['inward'])       ? $inv['inward']       : [];
$order        = isset($inv['order'])        ? $inv['order']        : [];
$gross_weight = isset($inv['gross_weight']) ? $inv['gross_weight'] : new stdClass();
$tare_weight  = isset($inv['tare_weight'])  ? $inv['tare_weight']  : new stdClass();
$gate_out     = isset($inv['gate_out'])     ? $inv['gate_out']     : new stdClass();

$inward_history  = isset($inward['history'])   ? $inward['history']   : [];
$order_history_0 = isset($order['history'][0]) ? $order['history'][0] : [];

// =============================================
// GATEIN — stdClass Object (->)
// =============================================
$GateINID     = isset($gatein->GateINID)       ? $gatein->GateINID                                       : '';
$gateInNo     = isset($inward['gatein_no'])    ? $inward['gatein_no']                                    : 'N/A';
$gateInTime   = isset($gatein->TransDate)      ? date('d-m-Y H:i', strtotime($gatein->TransDate))        : 'N/A';
$asnDate      = (isset($gatein->ASNDate) && $gatein->ASNDate != '')
                    ? date('d-m-Y H:i', strtotime($gatein->ASNDate))
                    : '-';
$location     = isset($gatein->LocationName)   ? $gatein->LocationName                                   : 'N/A';
$vehicleNo    = isset($gatein->VehicleNo)      ? $gatein->VehicleNo                                      : 'N/A';
$driverName   = isset($gatein->DriverName)     ? $gatein->DriverName                                     : '-';
$driverMobile = isset($gatein->DriverMobileNo) ? $gatein->DriverMobileNo                                 : '-';
$actualWeight = isset($gatein->ActualWeight)   ? $gatein->ActualWeight                                   : '-';
$bagWeight    = isset($gatein->BagWeight)      ? $gatein->BagWeight                                      : '-';
$totalDeduct  = isset($gatein->TotalDeduction) ? $gatein->TotalDeduction                                 : '0';
$gateinNetAmt = isset($gatein->NetAmt)         ? $gatein->NetAmt                                         : '0';

// =============================================
// INWARD — Array ([])
// =============================================
$bookingID    = isset($inward['InwardsID'])     ? $inward['InwardsID']                                   : 'N/A';
$purchaseID   = isset($inward['OrderID'])       ? $inward['OrderID']                                     : 'N/A';
$bookingDate  = isset($inward['TransDate'])     ? date('d-m-Y', strtotime($inward['TransDate']))         : 'N/A';
$paymentTerms = isset($inward['PaymentTerms'])  ? $inward['PaymentTerms']                                : 'N/A';
$orderID      = isset($inward['OrderID'])       ? $inward['OrderID']                                     : 'N/A';
$partyName    = isset($inward['company'])       ? $inward['company']                                     : 'N/A';
$totalWeight  = isset($inward['TotalWeight'])   ? $inward['TotalWeight']                                 : 'N/A';
$noOfBags     = isset($inward['TotalQuantity']) ? $inward['TotalQuantity']                               : 'N/A';
$netAmt       = isset($inward['NetAmt'])        ? $inward['NetAmt']                                      : 'N/A';
$taxableAmt   = isset($inward['TaxableAmt'])    ? $inward['TaxableAmt']                                  : 'N/A';
$cgstAmt      = isset($inward['CGSTAmt'])       ? $inward['CGSTAmt']                                     : '0';
$sgstAmt      = isset($inward['SGSTAmt'])       ? $inward['SGSTAmt']                                     : '0';
$igstAmt      = isset($inward['IGSTAmt'])       ? $inward['IGSTAmt']                                     : '0';
$inwardGSTIN  = isset($inward['GSTIN'])         ? $inward['GSTIN']                                       : '-';

// =============================================
// GATE OUT — stdClass Object (->)
// =============================================
$gateOutTime  = isset($gate_out->value->Time)
                    ? date('d-m-Y H:i', strtotime($gate_out->value->Time))
                    : '-';

// =============================================
// ORDER — Array ([])
// =============================================
$vendorDocNo   = isset($order['VendorDocNo'])      ? $order['VendorDocNo']                               : '-';
$vendorDocDate = isset($order['VendorDocDate'])    ? date('d-m-Y', strtotime($order['VendorDocDate']))   : '-';
$gstin         = (isset($order['GSTIN']) && $order['GSTIN'] !== '') ? $order['GSTIN']                    : '-';
$internalRmk   = isset($order['Internal_Remarks']) ? $order['Internal_Remarks']                          : '-';
$docRemark     = isset($order['Document_Remark'])  ? $order['Document_Remark']                           : '-';

// =============================================
// TTYPE — Order history[0]  (TType = 'P')
// =============================================
$ttype = isset($order_history_0['TType']) ? strtoupper(trim($order_history_0['TType'])) : 'P';
if ($ttype === '') $ttype = 'P';

$chkDeposit    = ($ttype == 'D') ? '&#10004;' : '&#9744;';
$chkWithdrawal = ($ttype == 'W') ? '&#10004;' : '&#9744;';
$chkSales      = ($ttype == 'S') ? '&#10004;' : '&#9744;';
$chkPurchase   = ($ttype == 'P') ? '&#10004;' : '&#9744;';
$chkAnamat     = ($ttype == 'A') ? '&#10004;' : '&#9744;';
$chkTF         = ($ttype == 'T') ? '&#10004;' : '&#9744;';

// =============================================
// GROSS WEIGHT — stdClass Object (->)
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
        $grossWeightVal = floatval($gwVal->gross_weight);
    }
}

// =============================================
// TARE WEIGHT — stdClass Object (->)
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
        $tareWeightVal = floatval($twVal->tare_weight);
    }
}

// NET WEIGHT = Gross - Tare
$netWeight = '-';
if ($grossWeightVal !== '-' && $tareWeightVal !== '-') {
    $netWeight = floatval($grossWeightVal) - floatval($tareWeightVal);
}

// =============================================
// COMMODITY ROWS — Inward history loop (3 items)
// =============================================
$commodityRows = '';
foreach ($inward_history as $h) {
    $h_commodity  = isset($h['item_name'])  ? $h['item_name']          : 'N/A';
    $h_unit       = isset($h['SuppliedIn']) ? $h['SuppliedIn']         : 'MT';
    $h_basicRate  = isset($h['BasicRate'])  ? number_format(floatval($h['BasicRate']), 2) : 'N/A';
    $h_orderQty   = isset($h['OrderQty'])   ? $h['OrderQty']           : 'N/A';
    $h_unitWeight = isset($h['UnitWeight']) ? $h['UnitWeight']         : 'N/A';
    $h_cgst       = isset($h['cgst'])       ? $h['cgst']               : '0';
    $h_sgst       = isset($h['sgst'])       ? $h['sgst']               : '0';
    $h_igst       = isset($h['igst'])       ? $h['igst']               : '0';
    $h_cgstamt    = isset($h['cgstamt'])    ? $h['cgstamt']            : '0';
    $h_sgstamt    = isset($h['sgstamt'])    ? $h['sgstamt']            : '0';
    $h_igstamt    = isset($h['igstamt'])    ? $h['igstamt']            : '0';
    $h_orderAmt   = isset($h['OrderAmt'])   ? $h['OrderAmt']           : '0';
    $h_netOrderAmt= isset($h['NetOrderAmt'])? $h['NetOrderAmt']        : '0';
    $h_discAmt    = isset($h['DiscAmt'])    ? $h['DiscAmt']            : '0';

    $commodityRows .= '
    <tr>
        <td colspan="4" style="background-color:#e8f0fe; font-weight:bold; padding:3px 5px;">
            &#9654; ' . $h_commodity . '
        </td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Basic Rate</b></td>
        <td>Rs. ' . $h_basicRate . '</td>
        <td style="background-color:#f5f5f5;"><b>Unit</b></td>
        <td>' . $h_unit . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Order Qty</b></td>
        <td>' . $h_orderQty . ' ' . $h_unit . '</td>
        <td style="background-color:#f5f5f5;"><b>Unit Weight</b></td>
        <td>' . $h_unitWeight . ' ' . $h_unit . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Order Amt</b></td>
        <td>Rs. ' . $h_orderAmt . '</td>
        <td style="background-color:#f5f5f5;"><b>Disc Amt</b></td>
        <td>Rs. ' . $h_discAmt . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>CGST ' . $h_cgst . '%</b></td>
        <td>Rs. ' . $h_cgstamt . '</td>
        <td style="background-color:#f5f5f5;"><b>SGST ' . $h_sgst . '%</b></td>
        <td>Rs. ' . $h_sgstamt . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>IGST ' . $h_igst . '%</b></td>
        <td>Rs. ' . $h_igstamt . '</td>
        <td style="background-color:#f5f5f5;"><b>Net Order Amt</b></td>
        <td><b>Rs. ' . $h_netOrderAmt . '</b></td>
    </tr>';
}

// =============================================
// PDF SETUP
// =============================================
$pdf->SetMargins(10, 10, 10);
$pdf->SetFont('dejavusans', '', 9);

$html = '
<table border="1" cellpadding="5" cellspacing="0" width="100%">

    <!-- ===== COMPANY HEADER ===== -->
    <tr>
        <td colspan="4" align="center" style="background-color:#d9e1f2;">
            <b>
            <span style="font-size:16pt;">' . $PlantDetail->company_name . '</span><br/>
            <span style="font-size:9pt;">
                ' . $PlantDetail->address . '<br/>
                GSTIN: ' . $PlantDetail->gst . ' &nbsp;|&nbsp; Contact: ' . $PlantDetail->mobile1 . '<br/>
                Email: ' . $PlantDetail->BusinessEmail . '
            </span>
            </b>
        </td>
    </tr>
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea; font-size:12pt;">
            <b>GATE IN PASS</b>
        </td>
    </tr>

    <!-- ===== BASIC DETAILS ===== -->
    <tr>
        <td width="22%" style="background-color:#f5f5f5;"><b>GateIn No</b></td>
        <td width="28%">' . $gateInNo . '</td>
        <td width="22%" style="background-color:#f5f5f5;"><b>GateIN ID</b></td>
        <td width="28%">' . $GateINID . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Inward ID</b></td>
        <td>' . $bookingID . '</td>
        <td style="background-color:#f5f5f5;"><b>Purchase Order</b></td>
        <td>' . $purchaseID . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Booking Date</b></td>
        <td>' . $bookingDate . '</td>
        <td style="background-color:#f5f5f5;"><b>GateIn Time</b></td>
        <td>' . $gateInTime . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>GateOut Time</b></td>
        <td>' . $gateOutTime . '</td>
        <td style="background-color:#f5f5f5;"><b>ASN Date</b></td>
        <td>' . $asnDate . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Location</b></td>
        <td>' . $location . '</td>
        <td style="background-color:#f5f5f5;"><b>Payment Terms</b></td>
        <td>' . $paymentTerms . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Order ID</b></td>
        <td>' . $orderID . '</td>
        <td style="background-color:#f5f5f5;"><b>GSTIN</b></td>
        <td>' . $gstin . '</td>
    </tr>

    <!-- ===== TRANSACTION TYPE ===== -->
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

    <!-- ===== PARTY DETAILS ===== -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Party Details</b></td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Party Name</b></td>
        <td colspan="3">' . $partyName . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Vehicle No</b></td>
        <td>' . $vehicleNo . '</td>
        <td style="background-color:#f5f5f5;"><b>Driver Name</b></td>
        <td>' . $driverName . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Driver Mobile</b></td>
        <td>' . $driverMobile . '</td>
        <td style="background-color:#f5f5f5;"><b>Vendor Doc No</b></td>
        <td>' . $vendorDocNo . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Vendor Doc Date</b></td>
        <td>' . $vendorDocDate . '</td>
        <td style="background-color:#f5f5f5;"><b>No of Bags</b></td>
        <td>' . $noOfBags . '</td>
    </tr>

    <!-- ===== COMMODITY DETAILS (loop) ===== -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Commodity Details</b></td>
    </tr>
    ' . $commodityRows . '

    <!-- ===== WEIGHT DETAILS ===== -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Weight Details</b></td>
    </tr>
    <tr>
        <td align="center" style="background-color:#f5f5f5;"><b>Gross Weight</b></td>
        <td align="center" style="background-color:#f5f5f5;"><b>Tare Weight</b></td>
        <td align="center" style="background-color:#f5f5f5;"><b>Net Weight</b></td>
        <td align="center" style="background-color:#f5f5f5;"><b>Actual Weight</b></td>
    </tr>
    <tr>
        <td align="center">' . $grossWeightVal . '</td>
        <td align="center">' . $tareWeightVal  . '</td>
        <td align="center">' . $netWeight      . '</td>
        <td align="center">' . $actualWeight   . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Bag Weight</b></td>
        <td>' . $bagWeight . '</td>
        <td style="background-color:#f5f5f5;"><b>Total Weight</b></td>
        <td>' . $totalWeight . '</td>
    </tr>

    <!-- ===== AMOUNT SUMMARY ===== -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Amount Summary</b></td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Taxable Amount</b></td>
        <td>Rs. ' . $taxableAmt . '</td>
        <td style="background-color:#f5f5f5;"><b>CGST Amt</b></td>
        <td>Rs. ' . $cgstAmt . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>SGST Amt</b></td>
        <td>Rs. ' . $sgstAmt . '</td>
        <td style="background-color:#f5f5f5;"><b>IGST Amt</b></td>
        <td>Rs. ' . $igstAmt . '</td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Total Deduction</b></td>
        <td>Rs. ' . $totalDeduct . '</td>
        <td style="background-color:#bdd0ea;"><b>Net Amount</b></td>
        <td style="background-color:#bdd0ea;"><b>Rs. ' . $netAmt . '</b></td>
    </tr>

    <!-- ===== REMARKS ===== -->
    <tr>
        <td colspan="4" align="center" style="background-color:#bdd0ea;"><b>Remarks</b></td>
    </tr>
    <tr>
        <td style="background-color:#f5f5f5;"><b>Internal Remark</b></td>
        <td>' . $internalRmk . '</td>
        <td style="background-color:#f5f5f5;"><b>Document Remark</b></td>
        <td>' . $docRemark . '</td>
    </tr>

    <!-- ===== SIGNATURE ===== -->
    <tr style="height:60px;">
        <td colspan="2" align="center">
            <br/><br/>____________________________<br/>
            Signature &amp; Name of Driver
        </td>
        <td colspan="2" align="center">
            <br/><br/>____________________________<br/>
            Signature of WH Manager / Supervisor
        </td>
    </tr>

</table>
';

$pdf->writeHTML($html, true, false, false, false, '');