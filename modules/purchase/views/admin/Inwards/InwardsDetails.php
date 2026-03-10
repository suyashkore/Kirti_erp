<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
  table {
    border-collapse: collapse;
    width: 100%;
  }

  th,
  td {
    padding: 1px 5px !important;
    white-space: nowrap;
    border: 1px solid !important;
    font-size: 11px;
    line-height: 1.42857143 !important;
    vertical-align: middle !important;
  }

  th {
    background: #50607b;
    color: #fff !important;
  }

  .sortable {
    cursor: pointer;
  }

  .upload-label {
    transition: background-color 0.3s, color 0.3s, border-color 0.3s;
    cursor: pointer;
  }

  .upload-label.uploaded {
    background-color: #5cb85c !important;
    border-color: #4cae4c !important;
    color: #fff !important;
  }

  .upload-label.uploaded i {
    margin-right: 3px;
  }

  #itemDetailModal .modal-header {
    background: #50607b;
    color: #fff;
    padding: 10px 15px;
  }

  #itemDetailModal .modal-header .close {
    color: #fff;
    opacity: 1;
  }

  #itemDetailModal .modal-body {
    padding: 15px;
  }

  #itemDetailModal table th {
    background: #50607b;
    color: #fff !important;
    font-size: 12px;
  }

  #itemDetailModal table td {
    font-size: 12px;
  }

  .item-select-dropdown {
    min-width: 130px;
  }

  .qc-display-input {
    display: none !important;
  }

  .qc-badge-wrapper {
    display: inline-flex;
    flex-direction: column;
    gap: 2px;
    vertical-align: middle;
    max-width: 180px;
  }

  .qc-badge {
    display: block;
    background-color: #dff0d8;
    border: 1px solid #5cb85c;
    color: #3c763d;
    border-radius: 3px;
    padding: 1px 6px;
    font-size: 10px;
    font-weight: bold;
    white-space: nowrap;
  }

  .qc-badge-empty {
    color: #aaa;
    font-size: 10px;
    font-style: italic;
  }

  .qc-cell-inner {
    display: inline-flex;
    align-items: flex-start;
    gap: 4px;
  }

  .actual-weight-value {
    font-weight: bold;
    color: #3c763d;
  }

  .heading-with-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
  }

  .heading-with-btn h4 {
    margin: 0 !important;
  }

  /* ====== DEDUCTION MATRIX MODAL TABLE ====== */
  #deductionMatrixSection {
    margin-top: 15px;
    border-top: 2px solid #50607b;
    padding-top: 10px;
  }

  #deductionMatrixSection h6 {
    color: #50607b;
    font-weight: bold;
    margin-bottom: 6px;
  }

  .dm-table thead th {
    background: #50607b;
    color: #fff !important;
    font-size: 11px;
  }

  .dm-table td {
    font-size: 11px;
    padding: 2px 5px !important;
  }

  .dm-highlight {
    background-color: #fcf8e3 !important;
    font-weight: bold;
    color: #8a6d3b;
  }

  .dm-result-row td {
    background-color: #dff0d8 !important;
    font-weight: bold;
    color: #3c763d;
    font-size: 12px;
  }

  .qc-input-with-calc {
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .qc-value-input {
    min-width: 90px;
  }

  .qc-calc-badge {
    background: #d9edf7;
    border: 1px solid #5bc0de;
    color: #31708f;
    border-radius: 3px;
    padding: 1px 5px;
    font-size: 10px;
    white-space: nowrap;
  }

  /* ====== NEW DEDUCTION MATRIX TABLE (RIGHT SIDE) ====== */
  .dm-right-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  .dm-right-table thead th {
    background: #50607b;
    color: #fff !important;
    padding: 6px 5px !important;
    font-size: 11px;
    text-align: left;
    border: 1px solid #ccc !important;
  }

  .dm-right-table tbody td {
    padding: 4px 5px !important;
    border: 1px solid #ddd !important;
    font-size: 10px;
  }

  .dm-item-header-row {
    background-color: #e8f4fd !important;
    font-weight: bold;
    color: #31708f;
    border-left: 3px solid #5bc0de !important;
  }

  .dm-item-header-row td {
    padding: 5px 8px !important;
    font-size: 11px;
  }

  .dm-param-row {
    background-color: #f9f9f9 !important;
  }

  .dm-param-row td {
    text-align: left;
  }

  .dm-param-row td:nth-child(2),
  .dm-param-row td:nth-child(3),
  .dm-param-row td:nth-child(4) {
    text-align: right;
  }

  .dm-item-total {
    background-color: #dfe9f0 !important;
    font-weight: bold;
    color: #31708f;
    border-top: 2px solid #5bc0de !important;
  }

  .dm-item-total td {
    padding: 5px 8px !important;
  }

  .dm-item-total td:nth-child(2),
  .dm-item-total td:nth-child(3),
  .dm-item-total td:nth-child(4) {
    text-align: right;
  }

  .dm-final-rate-row {
    background-color: #dff0d8 !important;
    font-weight: bold;
    color: #3c763d;
  }

  .dm-final-rate-row td {
    padding: 5px 8px !important;
  }

  .dm-final-rate-row td:nth-child(4) {
    text-align: right;
  }

  .dm-summary-row {
    background-color: #d9edf7 !important;
    font-weight: bold;
    color: #31708f;
  }

  .dm-summary-row td {
    padding: 5px 8px !important;
  }

  .dm-summary-row td:nth-child(4) {
    text-align: right;
  }

  .deduction-value {
    color: #c0392b;
    font-weight: bold;
  }

  .amount-value {
    color: #27ae60;
    font-weight: bold;
  }

  /* Stack/Lot dropdown loading state */
  .sq-stack-select,
  .sq-lot-select {
    min-width: 120px;
  }
</style>

<?php
$gw = !empty($gross_weight) ? (object) $gross_weight : null;
if ($gw && isset($gw->value) && is_array($gw->value)) {
  $gw->value = (object) $gw->value;
}
$tw = !empty($tare_weight) ? (object) $tare_weight : null;
if ($tw && isset($tw->value) && is_array($tw->value)) {
  $tw->value = (object) $tw->value;
}
$cv = null;
if (!empty($conveyor)) {
  $cv = (object) $conveyor;
  if ($cv && isset($cv->value)) {
    if (is_array($cv->value)) {
      $cv->value = (object) $cv->value;
    }
    if (isset($cv->value->ConveyorID)) {
      $cv->value->conveyor_id = $cv->value->ConveyorID;
    }
  }
}

// ====== CHAMBER LIST ======
$chamberList = !empty($chamber) && is_array($chamber) ? $chamber : [];

$itemList = [];
if (!empty($inward['history']) && is_array($inward['history'])) {
  foreach ($inward['history'] as $histRow) {
    $itemList[] = [
      'ItemID' => $histRow['ItemID'] ?? '',
      'item_name' => $histRow['item_name'] ?? '',
      'BasicRate' => $histRow['BasicRate'] ?? '',
      'SaleRate' => $histRow['SaleRate'] ?? '',
      'UnitWeight' => $histRow['UnitWeight'] ?? '',
      'WeightUnit' => $histRow['WeightUnit'] ?? '',
      'OrderQty' => $histRow['OrderQty'] ?? '',
      'SuppliedIn' => $histRow['SuppliedIn'] ?? '',
      'OrderAmt' => $histRow['OrderAmt'] ?? '',
      'NetOrderAmt' => $histRow['NetOrderAmt'] ?? '',
      'igst' => $histRow['igst'] ?? '',
      'igstamt' => $histRow['igstamt'] ?? '',
      'cgst' => $histRow['cgst'] ?? '',
      'sgst' => $histRow['sgst'] ?? '',
      'batch_no' => $histRow['batch_no'] ?? '',
      'expiry_date' => $histRow['expiry_date'] ?? '',
    ];
  }
}

// ====== sqRows: $stack_qc_details  (NEW FORMAT) ======
$sqRows = !empty($stack_qc_details) && is_array($stack_qc_details) ? $stack_qc_details : [];

// ====== qcValuesStore PHP: parameter_id based ======
$qcValuesStorePhp = [];
foreach ($sqRows as $ri => $sqRow) {
  $sqRow = is_array($sqRow) ? $sqRow : (array) $sqRow;
  $qcArr = isset($sqRow['qc']) && is_array($sqRow['qc']) ? $sqRow['qc'] : [];
  foreach ($qcArr as $qcEntry) {
    $qcEntry = is_array($qcEntry) ? $qcEntry : (array) $qcEntry;
    $paramId = isset($qcEntry['parameter_id']) ? (string) $qcEntry['parameter_id'] : '';
    $paramVal = isset($qcEntry['value']) ? (string) $qcEntry['value'] : '';
    $dedAmt = isset($qcEntry['deductionamt']) ? (float) $qcEntry['deductionamt'] : 0;
    if ($paramId === '')
      continue;
    $qcValuesStorePhp[$ri][$paramId] = [
      'pct' => $paramVal,
      'paramName' => '',
      'parameter_id' => $paramId,
      'deduction' => null,
      'calcBy' => '',
      'reductionAmt' => $dedAmt,
      'basicRate' => 0
    ];
  }
}

$phpGrossWeight = isset($gw->value->gross_weight) ? (float) $gw->value->gross_weight : 0;
$phpTareWeight = isset($tw->value->tare_weight) ? (float) $tw->value->tare_weight : 0;
$phpActualWeight = ($phpGrossWeight > 0 && $phpTareWeight > 0) ? ($phpGrossWeight - $phpTareWeight) : null;

// ====== HELPER: Build Chamber Dropdown (PHP) ======
function buildChamberDropdownPhp($chamberList, $selectedId = '')
{
  $html = '<select name="chamber[]" class="form-control">';
  $html .= '<option value="" disabled ' . (empty($selectedId) ? 'selected' : '') . '>-- Select Chamber --</option>';
  foreach ($chamberList as $ch) {
    $isSelected = (!empty($selectedId) && $selectedId == $ch['id']) ? 'selected' : '';
    $html .= '<option value="' . htmlspecialchars($ch['id']) . '" ' . $isSelected . '>'
      . htmlspecialchars($ch['ChamberName'])
      . '</option>';
  }
  $html .= '</select>';
  return $html;
}
?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-10">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb"
                style="background-color: #fff !important; margin-bottom: 0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i
                        class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Inward</b></li>
              </ol>
            </nav>
            <hr class="hr_style" />
            <br />
            <div class="row">

              <!-- BOOKING DETAILS -->
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Booking Details:</h4>
                <hr class="hr_style" />
              </div>

              <div class="col-md-12 mbot5">
                <table>
                  <tbody>
                    <input type="hidden" name="gatein_id" id="gatein_id" value="<?= $inward['gatein_id'] ?? '-'; ?>">
                    <tr>
                      <td><b>Account ID : </b></td>
                      <td><?= $inward['AccountID'] ?? '-'; ?></td>
                      <td><b>Party Name : </b></td>
                      <td><?= $inward['company'] ?? '-'; ?></td>
                    </tr>
                    <tr>
                      <td><b>Order ID : </b></td>
                      <td><b><?= $inward['OrderID'] ?? '-'; ?></b></td>
                      <td><b>Party Type : </b></td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td><b>ASN By : </b></td>
                      <td>-</td>
                      <td><b>ASN Date: </b></td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td><b>ASN : </b></td>
                      <td><a href="" target="_blank">View ASN</a></td>
                      <td><b>Gate In Pass : </b></td>
                      <td><a href="<?= admin_url('purchase/Vehiclein/GateinPassPrint/' . $gatein->GateINID); ?>"
                          target="_blank">View Gate In Pass</a></td>
                    </tr>
                    <tr>
                      <td><b>ASN Quantity(KG): </b></td>
                      <td>-</td>
                      <td><b>ASN Quantity(Bag): </b></td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td><b>Gate In By : </b></td>
                      <td><?= $gatein->UserID ?? '-'; ?></td>
                      <td><b>Gate In Date : </b></td>
                      <td><?= date('d/m/Y', strtotime($gatein->TransDate ?? '0000-00-00')); ?></td>
                    </tr>
                    <tr>
                      <td><b>Trade Rate (KG) : </b></td>
                      <td>-</td>
                      <td><b>Vehicle No. : </b></td>
                      <td><?= $gatein->VehicleNo ?? '-'; ?></td>
                    </tr>
                    <tr>
                      <td><b>Center Name : </b></td>
                      <td><?= $gatein->LocationName ?? '-'; ?></td>
                      <td><b>Status : </b></td>
                      <td><?= $gatein->StatusName ?? '-'; ?></td>
                    </tr>
                    <tr>
                      <td><b>Party Invoice : </b></td>
                      <td><a href="" target="_blank">Click to View Party Invoice</a></td>
                      <td></td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- GROSS WEIGHT -->
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Gross Weight Details:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-12 mbot5">
                <form action="" method="post" id="gross_weight_form" enctype="multipart/form-data">
                  <input type="hidden" name="update_id" id="gw_update_id" value="<?= $gw->id ?? ''; ?>">
                  <input type="hidden" name="GateINID" id="gw_GateINID" value="<?= $gatein->GateINID; ?>">
                  <input type="hidden" name="form_mode" id="gw_form_mode" value="<?= $gw ? 'edit' : 'add'; ?>">
                  <table>
                    <tbody>
                      <tr>
                        <th>Total Weight(KG)</th>
                        <th>Top Image</th>
                        <th>Front Image</th>
                        <th>Side Image</th>
                        <th>Loaded By</th>
                        <th>Loaded Date Time</th>
                        <th>Action</th>
                      </tr>
                      <tr>
                        <td>
                          <?php if ($gw && isset($gw->value->gross_weight)): ?>
                            <span id="gw_weight_display"><?= $gw->value->gross_weight; ?></span>
                            <input type="text" name="gross_weight" id="gross_weight" class="form-control"
                              value="<?= $gw->value->gross_weight; ?>" style="display:none;" required>
                          <?php else: ?>
                            <span id="gw_weight_display" style="display:none;"></span>
                            <input type="text" name="gross_weight" id="gross_weight" class="form-control" required>
                          <?php endif; ?>
                        </td>
                        <td id="gw_top_image_cell">
                          <?php if ($gw && !empty($gw->value->TopImage)): ?>
                            <a href="<?= base_url($gw->value->TopImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_top_image" value="<?= $gw->value->TopImage; ?>">
                          <?php else: ?>
                            <input type="file" name="top_image" id="gw_top_image" accept="image/*" style="display:none;">
                            <label for="gw_top_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="gw_front_image_cell">
                          <?php if ($gw && !empty($gw->value->FrontImage)): ?>
                            <a href="<?= base_url($gw->value->FrontImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_front_image" value="<?= $gw->value->FrontImage; ?>">
                          <?php else: ?>
                            <input type="file" name="front_image" id="gw_front_image" accept="image/*"
                              style="display:none;">
                            <label for="gw_front_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="gw_side_image_cell">
                          <?php if ($gw && !empty($gw->value->SideImage)): ?>
                            <a href="<?= base_url($gw->value->SideImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_side_image" value="<?= $gw->value->SideImage; ?>">
                          <?php else: ?>
                            <input type="file" name="side_image" id="gw_side_image" accept="image/*"
                              style="display:none;">
                            <label for="gw_side_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="gw_loaded_by"><?= $gw->UserID ?? '-'; ?></td>
                        <td>
                          <?php if ($gw && !empty($gw->TransDate)): ?>
                            <span id="loadedDateTimeCell"
                              data-saved="1"><?= date('Y-m-d H:i:s', strtotime($gw->TransDate)); ?></span>
                          <?php else: ?>
                            <span id="loadedDateTimeCell"></span>
                          <?php endif; ?>
                        </td>
                        <td style="width:80px;">
                          <?php if ($gw): ?>
                            <button type="button" class="btn btn-warning btn-xs" id="gw_edit_btn" title="Edit"><i
                                class="fa fa-pencil"></i></button>
                            <button type="submit" class="btn btn-success btn-xs" id="gw_save_btn" style="display:none;"
                              title="Save"><i class="fa fa-save"></i></button>
                          <?php else: ?>
                            <button type="button" class="btn btn-warning btn-xs" id="gw_edit_btn" title="Edit"
                              style="display:none;"><i class="fa fa-pencil"></i></button>
                            <button type="submit" class="btn btn-success btn-xs" id="gw_save_btn" title="Save"><i
                                class="fa fa-save"></i></button>
                          <?php endif; ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </form>
              </div>

              <!-- CONVEYOR -->
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12 mbot5">
                    <h4 class="bold p_style">Conveyor Assigned:</h4>
                    <hr class="hr_style" />
                  </div>
                  <div class="col-md-12 mbot5">
                    <form action="" method="post" id="conveyor_form">
                      <input type="hidden" name="update_id" id="cv_update_id" value="<?= $cv->id ?? ''; ?>">
                      <input type="hidden" name="GateINID" id="cv_GateINID" value="<?= $gatein->GateINID; ?>">
                      <input type="hidden" name="form_mode" id="cv_form_mode" value="<?= $cv ? 'edit' : 'add'; ?>">
                      <table>
                        <tbody>
                          <tr>
                            <th>Conveyor</th>
                            <th>Added By</th>
                            <th>Date Time</th>
                            <th>Action</th>
                          </tr>
                          <tr>
                            <td>
                              <select name="conveyor_id" id="conveyor_id" class="form-control" <?= ($cv && !empty($cv->value->conveyor_id)) ? 'disabled' : ''; ?>>
                                <option value="" disabled <?= (!$cv || empty($cv->value->conveyor_id)) ? 'selected' : ''; ?>>None selected</option>
                                <?php foreach (['1', '2', '3', '4'] as $c): ?>
                                  <option value="<?= $c; ?>" <?= ($cv && !empty($cv->value->conveyor_id) && $cv->value->conveyor_id == $c) ? 'selected' : ''; ?>>Conveyor <?= $c; ?></option>
                                <?php endforeach; ?>
                              </select>
                            </td>
                            <td id="cv_added_by"><?= $cv->UserID ?? '-'; ?></td>
                            <td>
                              <?php if ($cv && !empty($cv->TransDate)): ?>
                                <span id="cv_datetime_cell"
                                  data-saved="1"><?= date('Y-m-d H:i:s', strtotime($cv->TransDate)); ?></span>
                              <?php else: ?>
                                <span id="cv_datetime_cell"></span>
                              <?php endif; ?>
                            </td>
                            <td style="width:80px;">
                              <?php if ($cv): ?>
                                <button type="button" class="btn btn-warning btn-xs" id="cv_edit_btn" title="Edit"><i
                                    class="fa fa-pencil"></i></button>
                                <button type="submit" class="btn btn-success btn-xs" id="cv_save_btn"
                                  style="display:none;" title="Save"><i class="fa fa-save"></i></button>
                              <?php else: ?>
                                <button type="button" class="btn btn-warning btn-xs" id="cv_edit_btn" title="Edit"
                                  style="display:none;"><i class="fa fa-pencil"></i></button>
                                <button type="submit" class="btn btn-success btn-xs" id="cv_save_btn" title="Save"><i
                                    class="fa fa-save"></i></button>
                              <?php endif; ?>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </form>
                  </div>
                </div>
              </div>

              <!-- QC & STACK -->
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Center QC & Stack Details:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-12 mbot5">
                <form id="stack_qc_form">
                  <input type="hidden" name="GateINID" id="sq_GateINID" value="<?= $gatein->GateINID; ?>">
                  <input type="hidden" name="form_mode" id="sq_form_mode"
                    value="<?= !empty($sqRows) ? 'edit' : 'add'; ?>">
                  <input type="hidden" name="update_id" id="sq_update_id"
                    value="<?= !empty($stack_qc) ? (is_array($stack_qc) ? ($stack_qc['id'] ?? '') : ($stack_qc->id ?? '')) : ''; ?>">
                  <input type="hidden" name="sq_gatein_id" id="sq_gatein_id" value="<?= $inward['gatein_id'] ?? ''; ?>">

                  <table class="mbot5" id="stack_qc_table">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Chamber</th>
                        <th>Stack</th>
                        <th>Lot</th>
                        <th>Weight</th>
                        <th>Bag Qty</th>
                        <th>QC Values</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody id="stack_qc_tbody">

                      <?php if (!empty($sqRows)): ?>
                        <?php foreach ($sqRows as $ri => $sqRow):
                          $sqRow = is_array($sqRow) ? $sqRow : (array) $sqRow;
                          $savedItemId = htmlspecialchars($sqRow['item_id'] ?? '');
                          $savedChamberId = $sqRow['chamber'] ?? '';
                          $savedStackId = $sqRow['stack'] ?? '';
                          $savedLotId = $sqRow['lot'] ?? '';
                          $savedWeight = htmlspecialchars($sqRow['weight'] ?? '');
                          $savedBagQty = htmlspecialchars($sqRow['bag_qty'] ?? '');
                          $savedRowDbId = htmlspecialchars($sqRow['id'] ?? '');
                          // ====== CHANGE 1: UOM from stack_qc_details ======
                          $savedUom = htmlspecialchars($sqRow['uom'] ?? '');
                          $savedQcArr = isset($sqRow['qc']) && is_array($sqRow['qc']) ? $sqRow['qc'] : [];

                          // Fallback: item list madhe WeightUnit shodha
                          $savedWeightUnit = $savedUom;
                          if (empty($savedWeightUnit)) {
                            foreach ($itemList as $itm) {
                              if ($itm['ItemID'] == $sqRow['item_id']) {
                                $savedWeightUnit = $itm['WeightUnit'] ?? 'KG';
                                break;
                              }
                            }
                          }
                          if (empty($savedWeightUnit)) $savedWeightUnit = 'KG';
                          ?>
                          <tr data-row="<?= $ri; ?>" data-saved-chamber="<?= htmlspecialchars($savedChamberId); ?>"
                            data-saved-stack="<?= htmlspecialchars($savedStackId); ?>"
                            data-saved-lot="<?= htmlspecialchars($savedLotId); ?>" data-row-db-id="<?= $savedRowDbId; ?>"
                            data-uom="<?= htmlspecialchars($savedWeightUnit); ?>">
                            <td class="text-center row-num"><?= $ri + 1; ?></td>
                            <td>
                              <select name="item_id[]" class="form-control item-select-dropdown sq-item-select"
                                style="min-width:130px;">
                                <option value="" disabled>-- Select Item --</option>
                                <?php foreach ($itemList as $itm): ?>
                                  <option value="<?= htmlspecialchars($itm['ItemID']); ?>"
                                    data-name="<?= htmlspecialchars($itm['item_name']); ?>" <?= ($savedItemId == $itm['ItemID']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($itm['item_name']); ?>
                                  </option>
                                <?php endforeach; ?>
                              </select>
                            </td>
                            <td><?= buildChamberDropdownPhp($chamberList, $savedChamberId); ?></td>
                            <td class="stack-td">
                              <select name="stack[]" class="form-control sq-stack-select">
                                <option value="" disabled selected>Loading...</option>
                              </select>
                            </td>
                            <td class="lot-td">
                              <select name="lot[]" class="form-control sq-lot-select">
                                <option value="" disabled selected>-- Select Lot --</option>
                              </select>
                            </td>
                            <td>
                              <!-- ====== CHANGE 2: UOM label show from saved data ====== -->
                              <div style="display:flex; align-items:center; gap:4px;">
                                <input type="text" name="weight[]" class="form-control" value="<?= $savedWeight; ?>"
                                  style="min-width:70px;">
                                <span class="weight-unit-label" style="font-size:10px; color:#31708f; background:#d9edf7; border:1px solid #5bc0de; border-radius:3px; padding:1px 5px; white-space:nowrap;"><?= htmlspecialchars($savedWeightUnit); ?></span>
                              </div>
                            </td>
                            <td><input type="text" name="bag_qty[]" class="form-control" value="<?= $savedBagQty; ?>"></td>
                            <td style="white-space:nowrap;">
                              <input type="text" class="form-control qc-display-input" name="qc[]" value="" data-saved-qc=""
                                data-qc-json="<?= htmlspecialchars(json_encode($savedQcArr), ENT_QUOTES); ?>">
                              <div class="qc-cell-inner">
                                <button type="button" class="btn btn-info btn-xs sq-item-info-btn"
                                  title="View / Fill QC Details" style="margin-left:3px; align-self:flex-start;">
                                  <i class="fa fa-info-circle"></i>
                                </button>
                              </div>
                            </td>
                            <td style="width:60px; text-align:center;">
                              <button type="button" class="btn btn-success btn-xs sq-add-row" title="Add Row"><i
                                  class="fa fa-plus"></i></button>
                              <button type="button" class="btn btn-danger btn-xs sq-remove-row" title="Remove Row"><i
                                  class="fa fa-minus"></i></button>
                            </td>
                          </tr>
                        <?php endforeach; ?>

                      <?php else: ?>
                        <!-- DEFAULT EMPTY ROW -->
                        <tr data-row="0" data-saved-chamber="" data-saved-stack="" data-saved-lot="" data-row-db-id="" data-uom="KG">
                          <td class="text-center row-num">1</td>
                          <td>
                            <select name="item_id[]" class="form-control item-select-dropdown sq-item-select"
                              style="min-width:130px;">
                              <option value="" disabled selected>-- Select Item --</option>
                              <?php foreach ($itemList as $itm): ?>
                                <option value="<?= htmlspecialchars($itm['ItemID']); ?>"
                                  data-name="<?= htmlspecialchars($itm['item_name']); ?>">
                                  <?= htmlspecialchars($itm['item_name']); ?>
                                </option>
                              <?php endforeach; ?>
                            </select>
                          </td>
                          <td><?= buildChamberDropdownPhp($chamberList); ?></td>
                          <td class="stack-td">
                            <select name="stack[]" class="form-control sq-stack-select">
                              <option value="" disabled selected>-- Select Stack --</option>
                            </select>
                          </td>
                          <td class="lot-td">
                            <select name="lot[]" class="form-control sq-lot-select">
                              <option value="" disabled selected>-- Select Lot --</option>
                            </select>
                          </td>
                          <td>
                            <div style="display:flex; align-items:center; gap:4px;">
                              <input type="text" name="weight[]" class="form-control" style="min-width:70px;">
                              <span class="weight-unit-label"
                                style="font-size:10px; color:#31708f; background:#d9edf7; border:1px solid #5bc0de; border-radius:3px; padding:1px 5px; white-space:nowrap;">KG</span>
                            </div>
                          </td>
                          <td><input type="text" name="bag_qty[]" class="form-control"></td>
                          <td style="white-space:nowrap;">
                            <input type="text" class="form-control qc-display-input" name="qc[]" value="" data-saved-qc=""
                              data-qc-json="[]">
                            <div class="qc-cell-inner">
                              <div class="qc-badge-wrapper" id="qc_badges_0">
                                <span class="qc-badge-empty">-- No QC --</span>
                              </div>
                              <button type="button" class="btn btn-info btn-xs sq-item-info-btn"
                                title="View / Fill QC Details" style="margin-left:3px; align-self:flex-start;">
                                <i class="fa fa-info-circle"></i>
                              </button>
                            </div>
                          </td>
                          <td style="width:60px; text-align:center;">
                            <button type="button" class="btn btn-success btn-xs sq-add-row" title="Add Row"><i
                                class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-danger btn-xs sq-remove-row" title="Remove Row"><i
                                class="fa fa-minus"></i></button>
                          </td>
                        </tr>
                      <?php endif; ?>

                    </tbody>
                  </table>

                  <button type="submit" class="btn btn-success" id="sq_update_btn">
                    <i class="fa fa-save"></i> UPDATE STACK DETAILS
                  </button>
                </form>
              </div>

              <!-- TARE WEIGHT -->
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Tare Weight Details:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-12 mbot5">
                <form action="" method="post" id="tare_weight_form" enctype="multipart/form-data">
                  <input type="hidden" name="update_id" id="tw_update_id" value="<?= $tw->id ?? ''; ?>">
                  <input type="hidden" name="GateINID" id="tw_GateINID" value="<?= $gatein->GateINID; ?>">
                  <input type="hidden" name="form_mode" id="tw_form_mode" value="<?= $tw ? 'edit' : 'add'; ?>">
                  <table>
                    <tbody>
                      <tr>
                        <th>Tare Weight(KG)</th>
                        <th>Top Image</th>
                        <th>Front Image</th>
                        <th>Side Image</th>
                        <th>Uploaded By</th>
                        <th>Uploaded Date Time</th>
                        <th>Action</th>
                      </tr>
                      <tr>
                        <td>
                          <?php if ($tw && isset($tw->value->tare_weight)): ?>
                            <span id="tw_weight_display"><?= $tw->value->tare_weight; ?></span>
                            <input type="text" name="tare_weight" id="tare_weight" class="form-control"
                              value="<?= $tw->value->tare_weight; ?>" style="display:none;" required>
                          <?php else: ?>
                            <span id="tw_weight_display" style="display:none;"></span>
                            <input type="text" name="tare_weight" id="tare_weight" class="form-control" required>
                          <?php endif; ?>
                        </td>
                        <td id="tw_top_image_cell">
                          <?php if ($tw && !empty($tw->value->TopImage)): ?>
                            <a href="<?= base_url($tw->value->TopImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_top_image" value="<?= $tw->value->TopImage; ?>">
                          <?php else: ?>
                            <input type="file" name="top_image" id="tw_top_image" accept="image/*" style="display:none;">
                            <label for="tw_top_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="tw_front_image_cell">
                          <?php if ($tw && !empty($tw->value->FrontImage)): ?>
                            <a href="<?= base_url($tw->value->FrontImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_front_image" value="<?= $tw->value->FrontImage; ?>">
                          <?php else: ?>
                            <input type="file" name="front_image" id="tw_front_image" accept="image/*"
                              style="display:none;">
                            <label for="tw_front_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="tw_side_image_cell">
                          <?php if ($tw && !empty($tw->value->SideImage)): ?>
                            <a href="<?= base_url($tw->value->SideImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_side_image" value="<?= $tw->value->SideImage; ?>">
                          <?php else: ?>
                            <input type="file" name="side_image" id="tw_side_image" accept="image/*"
                              style="display:none;">
                            <label for="tw_side_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="tw_uploaded_by"><?= $tw->UserID ?? '-'; ?></td>
                        <td>
                          <?php if ($tw && !empty($tw->TransDate)): ?>
                            <span id="UploadedDateTimeCell"
                              data-saved="1"><?= date('Y-m-d H:i:s', strtotime($tw->TransDate)); ?></span>
                          <?php else: ?>
                            <span id="UploadedDateTimeCell"></span>
                          <?php endif; ?>
                        </td>
                        <td style="width:80px;">
                          <?php if ($tw): ?>
                            <button type="button" class="btn btn-warning btn-xs" id="tw_edit_btn" title="Edit"><i
                                class="fa fa-pencil"></i></button>
                            <button type="submit" class="btn btn-success btn-xs" id="tw_save_btn" style="display:none;"
                              title="Save"><i class="fa fa-save"></i></button>
                          <?php else: ?>
                            <button type="button" class="btn btn-warning btn-xs" id="tw_edit_btn" title="Edit"
                              style="display:none;"><i class="fa fa-pencil"></i></button>
                            <button type="submit" class="btn btn-success btn-xs" id="tw_save_btn" title="Save"><i
                                class="fa fa-save"></i></button>
                          <?php endif; ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </form>
              </div>

              <!-- DEDUCTION MATRIX (FULL WIDTH) -->
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Deduction Matrix:</h4>
                <hr class="hr_style" />

                <table class="dm-right-table" id="deduction_matrix_table">
                  <thead>
                    <tr>
                      <th style="width: 35%;">Parameter</th>
                      <th style="width: 15%;">QC Value</th>
                      <th style="width: 20%;">Deduction</th>
                      <th style="width: 30%;">Amount</th>
                    </tr>
                  </thead>
                  <tbody id="deduction_matrix_tbody">
                    <tr style="text-align: center; color: #999; padding: 10px !important;">
                      <td colspan="4">Select items & enter QC values to see calculations...</td>
                    </tr>
                  </tbody>
                </table>

                <!-- SUMMARY SECTION -->
                <table class="dm-right-table mbot5" style="margin-top: 15px;">
                  <tbody>
                    <tr class="dm-summary-row">
                      <td colspan="2"><b>Gross Weight (KG):</b></td>
                      <td colspan="2" style="text-align: right;">
                        <?php echo !empty($gw->value->gross_weight) ? $gw->value->gross_weight : '-'; ?></td>
                    </tr>
                    <tr class="dm-summary-row">
                      <td colspan="2"><b>Tare Weight (KG):</b></td>
                      <td colspan="2" style="text-align: right;">
                        <?php echo !empty($tw->value->tare_weight) ? $tw->value->tare_weight : '-'; ?></td>
                    </tr>
                    <tr class="dm-summary-row">
                      <td colspan="2"><b>Actual Weight (KG):</b></td>
                      <td colspan="2" style="text-align: right;"><span id="actual_weight_display"
                          style="color: #3c763d; font-weight: bold;">
                          <?php
                          if ($phpActualWeight !== null)
                            echo number_format($phpActualWeight, 2, '.', '');
                          else
                            echo '-';
                          ?>
                        </span></td>
                    </tr>
                    <tr class="dm-summary-row">
                      <td colspan="2"><b>Total Deduction:</b></td>
                      <td colspan="2" style="text-align: right;"><b id="dm_total_deduction_val">-</b></td>
                    </tr>
                    <tr class="dm-summary-row">
                      <td colspan="2"><b>Bag Weight:</b></td>
                      <td colspan="2" style="text-align: right;"><b id="dm_bag_weight_val">-</b></td>
                    </tr>
                    <!-- ====== CHANGE 3: Net Amount label updated ====== -->
                    <tr class="dm-summary-row">
                      <td colspan="2"><b>Net Amount (Final Rate × Weight):</b></td>
                      <td colspan="2" style="text-align: right;"><b id="dm_net_amount_val">-</b></td>
                    </tr>
                  </tbody>
                </table>

                <button type="button" class="btn btn-success" id="advance_payment_btn">
                  <i class="fa fa-money"></i> ADVANCE PAYMENT
                </button>
              </div>

              <div class="clearfix"></div>

              <!-- GATE OUT PASS -->
              <div class="col-md-6 mbot5">
                <div class="row">
                  <div class="col-md-12 mbot5">
                    <div class="heading-with-btn">
                      <h4 class="bold p_style">Gate Out Pass:</h4>
                      <button type="button" class="btn btn-success btn-sm" id="gateout_pass_btn"
                        style="margin-left: 236px; background-color: #1986f3;" <?= !empty($gate_out) ? 'disabled' : ''; ?>>
                        <?php if (!empty($gate_out)): ?>
                          <i class="fa fa-check"></i> Gate Out Done
                        <?php else: ?>
                          <i class="fa fa-sign-out"></i> GATE OUT PASS
                        <?php endif; ?>
                      </button>
                    </div>
                    <hr class="hr_style" />
                  </div>
                  <div class="col-md-12 mbot5">
                    <table>
                      <tbody>
                        <tr>
                          <th>Gate Out By</th>
                          <th>Gate Out Date Time</th>
                        </tr>
                        <tr>
                          <td id="gateout_by_cell"><?= !empty($gate_out) ? $gate_out->UserID : '-'; ?></td>
                          <td id="gateout_datetime_cell">
                            <?= !empty($gate_out) && !empty($gate_out->value->Time) ? $gate_out->value->Time : '-'; ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <!-- EXIT MARKED -->
              <div class="col-md-6 mbot5">
                <div class="row">
                  <div class="col-md-12 mbot5">
                    <div class="heading-with-btn">
                      <h4 class="bold p_style">Exit Marked:</h4>
                      <button type="button" class="btn btn-success btn-sm" id="exit_marked_btn"
                        style="margin-left: 285px; background-color: #1986f3;" <?= !empty($gate_exit) ? 'disabled' : ''; ?>>
                        <?php if (!empty($gate_exit)): ?>
                          <i class="fa fa-check"></i> Exit Done
                        <?php else: ?>
                          <i class="fa fa-sign-out"></i> EXIT MARKED
                        <?php endif; ?>
                      </button>
                    </div>
                    <hr class="hr_style" />
                  </div>
                  <div class="col-md-12 mbot5">
                    <table>
                      <tbody>
                        <tr>
                          <th>Exit By</th>
                          <th>Exit Date Time</th>
                        </tr>
                        <tr>
                          <td id="exit_by_cell"><?= !empty($gate_exit) ? $gate_exit->UserID : '-'; ?></td>
                          <td id="exit_datetime_cell">
                            <?= !empty($gate_exit) && !empty($gate_exit->value->Time) ? $gate_exit->value->Time : '-'; ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div><!-- end row -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- IMAGE MODAL -->
<style>
  #imageModal .modal-dialog {
    max-height: 60vh;
    margin-top: 10vh;
    margin-bottom: 10vh;
  }

  #imageModal .modal-content {
    max-height: 60vh;
    overflow: auto;
  }
</style>
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Image Preview</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="Image" style="max-width:100%;max-height:50vh;" />
      </div>
    </div>
  </div>
</div>

<!-- ITEM DETAIL POPUP MODAL -->
<div class="modal fade" id="itemDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 860px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-info-circle"></i> &nbsp;<span id="itemModalTitle">Item Details</span>
          <small id="itemModalBasicRate"
            style="margin-left:12px; font-size:11px; background:#fff; color:#50607b; border-radius:3px; padding:1px 8px;"></small>
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="savedQcDisplay"
          style="display:none; margin-bottom:8px; padding:6px 10px; background:#dff0d8; border:1px solid #5cb85c; border-radius:4px; font-size:11px; color:#3c763d;">
          <b><i class="fa fa-check-circle"></i> Saved QC:</b> <span id="savedQcText"></span>
        </div>
        <h6 class="bold" style="margin-bottom:6px; color:#50607b;">QC Parameters</h6>
        <div id="itemQCTableWrapper">
          <table style="width:100%;" id="itemQCMainTable">
            <thead>
              <tr>
                <th>#</th>
                <th>Parameter Name</th>
                <th>Min</th>
                <th>Max</th>
                <th>Base</th>
                <th>Calc By</th>
                <th>QC Value</th>
                <th>Matched Deduction</th>
                <th>Final Amount</th>
              </tr>
            </thead>
            <tbody id="itemQCTableBody">
              <tr>
                <td colspan="9" class="text-center" style="padding:8px !important;">Select an item to load QC
                  parameters...</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div id="itemQCLoader" style="display:none; text-align:center; padding:10px;">
          <i class="fa fa-spinner fa-spin fa-2x" style="color:#50607b;"></i>
          <span style="margin-left:8px; font-size:13px;">Loading QC Parameters...</span>
        </div>
        <div id="deductionMatrixSection" style="display:none;">
          <h6><i class="fa fa-table"></i> Deduction Matrix — <span id="dmParamName"></span></h6>
          <div id="dmTableWrapper"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-sm" id="itemModalSaveQCBtn"><i class="fa fa-save"></i> Save QC
          Values</button>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
  var baseUrl = '<?= base_url(); ?>';
  function getAbsUrl(path) {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    return baseUrl + path.replace(/^\/+/, '');
  }

  // ======================== CHAMBER LIST FROM PHP ========================
  var chamberList = <?= json_encode($chamberList); ?>;

  // ======================== ITEM LIST FROM PHP ========================
  var itemDataList = <?= json_encode($itemList); ?>;
  var itemDataMap = {};
  itemDataList.forEach(function (item) { itemDataMap[item.ItemID] = item; });

  // ======================== QC VALUES STORE ========================
  var qcValuesStore = <?= json_encode($qcValuesStorePhp); ?>;
  var modalQCData = {};
  var currentModalRowIndex = null;
  var currentModalItemID = null;

  // ======================== HTML ESCAPE ========================
  function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  // ======================== GET ROW UOM ========================
  // ====== CHANGE 4: Helper function - row cha UOM milva ======
  function getRowUom(rowIdx) {
    var $row = $('#stack_qc_tbody tr[data-row="' + rowIdx + '"]');
    // Priority 1: tr madhe data-uom attribute
    var uom = $row.attr('data-uom') || '';
    if (uom) return uom;
    // Priority 2: item DataMap madhe WeightUnit
    var itemID = $row.find('.sq-item-select').val() || '';
    if (itemID && itemDataMap[itemID]) {
      uom = itemDataMap[itemID].WeightUnit || '';
    }
    return uom || 'KG';
  }

  // ======================== BUILD QC DISPLAY STRING ========================
  function buildQCDisplayString(rowIndex) {
    var stored = qcValuesStore[rowIndex];
    if (!stored || Object.keys(stored).length === 0) return '';
    var parts = [];
    $.each(stored, function (paramId, vals) {
      if (vals.pct !== '' && vals.pct !== undefined && vals.pct !== null) {
        var label = vals.paramName ? vals.paramName : ('Param#' + paramId);
        parts.push(label + ': ' + vals.pct);
      }
    });
    return parts.length === 0 ? '' : '[' + parts.join('], [') + ']';
  }

  // ======================== UPDATE QC BADGES ========================
  function updateQCBadgesWithParamNames(rowIdx) {
    var $badgeWrap = $('#qc_badges_' + rowIdx);
    var stored = qcValuesStore[rowIdx] || {};

    $badgeWrap.empty();
    var hasData = false;

    $.each(stored, function (paramId, vals) {
      if (vals.pct === '' || vals.pct === undefined || vals.pct === null) return;
      hasData = true;
      $badgeWrap.append(
        '<span class="qc-badge" data-param-id="' + escHtml(String(paramId)) + '">'
        + escHtml(vals.paramName ? vals.paramName : ('Param#' + paramId))
        + ': <b>' + escHtml(String(vals.pct)) + '</b></span>'
      );
    });

    if (!hasData) {
      $badgeWrap.append('<span class="qc-badge-empty">-- No QC --</span>');
    }

    var $row = $('#stack_qc_tbody tr[data-row="' + rowIdx + '"]');
    $row.find('.qc-display-input').val(buildQCDisplayString(rowIdx));
  }

  // ======================== ACTUAL WEIGHT CALCULATION ========================
  function calculateActualWeight() {
    var grossVal = parseFloat($('#gw_weight_display').text());
    if (isNaN(grossVal) || grossVal === 0) grossVal = parseFloat($('#gross_weight').val()) || 0;
    var tareVal = parseFloat($('#tw_weight_display').text());
    if (isNaN(tareVal) || tareVal === 0) tareVal = parseFloat($('#tare_weight').val()) || 0;
    if (grossVal > 0 && tareVal > 0) {
      $('#actual_weight_display').text((grossVal - tareVal).toFixed(2)).css('color', '#3c763d');
    } else {
      $('#actual_weight_display').text('-').css('color', '');
    }
  }

  $(document).on('input keyup', '#gross_weight', function () {
    calculateActualWeight();
    var tw = parseFloat($('#tare_weight').val()) || 0;
    if (tw > 0) {
      var gw = parseFloat($(this).val()) || 0;
      $(this).css('border-color', (tw >= gw && gw > 0) ? '#e74c3c' : '');
    }
  });

  $(document).on('input keyup', '#tare_weight', function () {
    calculateActualWeight();
    var grossVal = parseFloat($('#gw_weight_display').text());
    if (isNaN(grossVal) || grossVal === 0) grossVal = parseFloat($('#gross_weight').val()) || 0;
    var tareVal = parseFloat($(this).val()) || 0;
    if (grossVal > 0 && tareVal > 0) {
      if (tareVal >= grossVal) {
        $(this).css('border-color', '#e74c3c');
        $('#tare_weight_error').remove();
        $(this).after('<span id="tare_weight_error" style="color:#e74c3c; font-size:10px; display:block;">Tare Weight must be less than Gross Weight (' + grossVal + ' KG)</span>');
      } else {
        $(this).css('border-color', '#5cb85c');
        $('#tare_weight_error').remove();
      }
    } else {
      $(this).css('border-color', '');
      $('#tare_weight_error').remove();
    }
  });

  // ======================== QC INPUT VALIDATION ========================
  $(document).on('keypress', '.qc-Percentage_Wise-input', function (e) {
    var char = String.fromCharCode(e.which);
    var val = $(this).val();
    if (!/[0-9.]/.test(char)) { e.preventDefault(); return; }
    if (char === '.' && val.indexOf('.') !== -1) { e.preventDefault(); return; }
  });
  $(document).on('keydown', '.qc-Percentage_Wise-input', function (e) {
    var allowedKeys = [8, 9, 13, 27, 46, 37, 38, 39, 40, 110, 190];
    if (allowedKeys.indexOf(e.which) !== -1) return;
    if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].indexOf(e.which) !== -1) return;
  });

  // ======================== BUILD STACK DROPDOWN HTML ========================
  function buildStackDropdownHtml(stacks, selectedId) {
    selectedId = String(selectedId || '');
    var html = '<select name="stack[]" class="form-control sq-stack-select">'
      + '<option value="" disabled ' + (selectedId === '' ? 'selected' : '') + '>-- Select Stack --</option>';
    if (stacks && stacks.length > 0) {
      stacks.forEach(function (s) {
        var isSelected = (String(s.id) === selectedId) ? 'selected' : '';
        html += '<option value="' + escHtml(String(s.id)) + '" ' + isSelected + '>' + escHtml(s.StackName) + '</option>';
      });
    }
    html += '</select>';
    return html;
  }

  // ======================== BUILD LOT DROPDOWN HTML ========================
  function buildLotDropdownHtml(lots, selectedId) {
    selectedId = String(selectedId || '');
    var html = '<select name="lot[]" class="form-control sq-lot-select">'
      + '<option value="" disabled ' + (selectedId === '' ? 'selected' : '') + '>-- Select Lot --</option>';
    if (lots && lots.length > 0) {
      lots.forEach(function (l) {
        var isSelected = (String(l.id) === selectedId) ? 'selected' : '';
        html += '<option value="' + escHtml(String(l.id)) + '" ' + isSelected + '>'
          + escHtml(l.LotName || l.lot_name || l.LotCode || String(l.id))
          + '</option>';
      });
    }
    html += '</select>';
    return html;
  }

  // ======================== LOAD STACKS FOR ROW ========================
  function loadStacksForRow($row, chamberID, selectedStackId, selectedLotId) {
    if (!chamberID) {
      $row.find('.stack-td').html(buildStackDropdownHtml([], ''));
      $row.find('.lot-td').html(buildLotDropdownHtml([], ''));
      return;
    }
    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    $row.find('.stack-td').html('<select name="stack[]" class="form-control sq-stack-select"><option value="" disabled selected>Loading...</option></select>');
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/StackListByChamber'); ?>',
      type: 'POST',
      data: { chamber_id: chamberID, '<?= $this->security->get_csrf_token_name(); ?>': csrfToken },
      dataType: 'json',
      success: function (res) {
        var stacks = [];
        if (Array.isArray(res)) stacks = res;
        else if (res.success && Array.isArray(res.data)) stacks = res.data;
        else if (Array.isArray(res.data)) stacks = res.data;
        $row.find('.stack-td').html(buildStackDropdownHtml(stacks, selectedStackId));
        var currentStackId = $row.find('.sq-stack-select').val();
        if (currentStackId) {
          loadLotsForRow($row, currentStackId, selectedLotId);
        } else {
          $row.find('.lot-td').html(buildLotDropdownHtml([], ''));
        }
      },
      error: function () {
        $row.find('.stack-td').html(buildStackDropdownHtml([], ''));
        $row.find('.lot-td').html(buildLotDropdownHtml([], ''));
      }
    });
  }

  // ======================== LOAD LOTS FOR ROW ========================
  function loadLotsForRow($row, stackID, selectedLotId) {
    if (!stackID) {
      $row.find('.lot-td').html(buildLotDropdownHtml([], ''));
      return;
    }
    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    $row.find('.lot-td').html('<select name="lot[]" class="form-control sq-lot-select"><option value="" disabled selected>Loading...</option></select>');
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/LotListByStack'); ?>',
      type: 'POST',
      data: { stack_id: stackID, '<?= $this->security->get_csrf_token_name(); ?>': csrfToken },
      dataType: 'json',
      success: function (res) {
        var lots = [];
        if (Array.isArray(res)) lots = res;
        else if (res.success && Array.isArray(res.data)) lots = res.data;
        else if (Array.isArray(res.data)) lots = res.data;
        $row.find('.lot-td').html(buildLotDropdownHtml(lots, selectedLotId));
      },
      error: function () {
        $row.find('.lot-td').html(buildLotDropdownHtml([], ''));
      }
    });
  }

  // ======================== PAGE LOAD ========================
  $(document).ready(function () {
    calculateActualWeight();

    $('#stack_qc_tbody tr').each(function () {
      var $row = $(this);
      var chamberVal = $row.find('select[name="chamber[]"]').val();
      var savedStack = $row.data('saved-stack') || '';
      var savedLot = $row.data('saved-lot') || '';
      if (chamberVal) {
        loadStacksForRow($row, chamberVal, savedStack, savedLot);
      }
    });

    loadSavedQCData();
  });

  // ======================== LOAD SAVED QC DATA ========================
  function loadSavedQCData() {
    var itemsToFetch = {};
    var fetchCount = 0;
    var completedCount = 0;

    $('#stack_qc_tbody tr').each(function () {
      var rowIdx = parseInt($(this).data('row'));
      var itemID = $(this).find('.sq-item-select').val();
      if (!itemID) return;
      if (!itemsToFetch[itemID]) itemsToFetch[itemID] = [];
      itemsToFetch[itemID].push(rowIdx);
    });

    fetchCount = Object.keys(itemsToFetch).length;
    if (fetchCount === 0) { rebuildDeductionMatrixTable(); return; }

    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

    $.each(itemsToFetch, function (itemID, rowIdxList) {
      $.ajax({
        url: '<?= admin_url('purchase/Inwards/ItemQCList'); ?>',
        type: 'POST',
        data: { ItemID: itemID, '<?= $this->security->get_csrf_token_name(); ?>': csrfToken },
        dataType: 'json',
        success: function (res) {
          var qcRows = [];
          if (Array.isArray(res)) qcRows = res;
          else if (res.success == true && Array.isArray(res.data)) qcRows = res.data;
          else if (Array.isArray(res.data)) qcRows = res.data;

          var paramIdMap = {};
          qcRows.forEach(function (r) {
            paramIdMap[String(r.id)] = r;
          });

          rowIdxList.forEach(function (rowIdx) {
            modalQCData[rowIdx] = qcRows;

            var $row = $('#stack_qc_tbody tr[data-row="' + rowIdx + '"]');
            var qcJson = $row.find('.qc-display-input').data('qc-json') || [];
            if (typeof qcJson === 'string') {
              try { qcJson = JSON.parse(qcJson); } catch (e) { qcJson = []; }
            }

            var item = itemDataMap[itemID] || {};
            var basicRate = parseFloat(item.BasicRate) || 0;

            if (!qcValuesStore[rowIdx]) qcValuesStore[rowIdx] = {};

            qcJson.forEach(function (qcEntry) {
              qcEntry = (typeof qcEntry === 'object') ? qcEntry : {};
              var paramId = String(qcEntry.parameter_id || '');
              var paramVal = String(qcEntry.value || '');
              var dedAmt = parseFloat(qcEntry.deductionamt) || 0;
              if (!paramId) return;

              var qcRowData = paramIdMap[paramId] || null;
              var paramName = qcRowData ? (qcRowData.ParameterName || '') : ('Param#' + paramId);
              var calcBy = qcRowData ? (qcRowData.CalculationBy || '') : '';
              var deduction = qcRowData ? findDeduction(paramVal, qcRowData.deduction_matrix) : null;
              var reductionAmt = (deduction !== null && basicRate > 0)
                ? calcReductionAmount(calcBy, basicRate, deduction)
                : dedAmt;

              qcValuesStore[rowIdx][paramId] = {
                pct: paramVal,
                paramName: paramName,
                parameter_id: paramId,
                deduction: deduction,
                calcBy: calcBy,
                reductionAmt: reductionAmt,
                basicRate: basicRate
              };
            });

            updateQCBadgesWithParamNames(rowIdx);
          });

          completedCount++;
          if (completedCount === fetchCount) {
            setTimeout(function () { rebuildDeductionMatrixTable(); }, 100);
          }
        },
        error: function () {
          completedCount++;
          if (completedCount === fetchCount) {
            setTimeout(function () { rebuildDeductionMatrixTable(); }, 100);
          }
        }
      });
    });
  }

  // ======================== FIND DEDUCTION (LINEAR INTERPOLATION) ========================
  function findDeduction(qcValue, deductionMatrix) {
    if (!deductionMatrix || deductionMatrix.length === 0) return null;
    var qv = parseFloat(qcValue);
    if (isNaN(qv)) return null;

    var sorted = deductionMatrix.slice().sort(function (a, b) {
      return parseFloat(a.Value) - parseFloat(b.Value);
    });

    for (var i = 0; i < sorted.length; i++) {
      if (parseFloat(sorted[i].Value) === qv) return parseFloat(sorted[i].Deduction);
    }
    if (qv < parseFloat(sorted[0].Value)) return parseFloat(sorted[0].Deduction);
    if (qv > parseFloat(sorted[sorted.length - 1].Value)) return parseFloat(sorted[sorted.length - 1].Deduction);

    for (var i = 0; i < sorted.length - 1; i++) {
      var v1 = parseFloat(sorted[i].Value);
      var v2 = parseFloat(sorted[i + 1].Value);
      var d1 = parseFloat(sorted[i].Deduction);
      var d2 = parseFloat(sorted[i + 1].Deduction);
      if (qv >= v1 && qv <= v2) {
        var interpolated = d1 + ((qv - v1) / (v2 - v1)) * (d2 - d1);
        return Math.round(interpolated * 100) / 100;
      }
    }
    return parseFloat(sorted[sorted.length - 1].Deduction);
  }

  // ======================== GET MATCHED MATRIX VALUE ========================
  function getMatchedMatrixValue(qcValue, deductionMatrix) {
    if (!deductionMatrix || deductionMatrix.length === 0) return null;
    var qv = parseFloat(qcValue);
    if (isNaN(qv)) return null;
    var sorted = deductionMatrix.slice().sort(function (a, b) {
      return parseFloat(a.Value) - parseFloat(b.Value);
    });
    for (var i = 0; i < sorted.length; i++) {
      if (parseFloat(sorted[i].Value) === qv) return parseFloat(sorted[i].Value);
    }
    if (qv < parseFloat(sorted[0].Value)) return parseFloat(sorted[0].Value);
    if (qv > parseFloat(sorted[sorted.length - 1].Value)) return parseFloat(sorted[sorted.length - 1].Value);
    for (var i = 0; i < sorted.length - 1; i++) {
      var v1 = parseFloat(sorted[i].Value);
      var v2 = parseFloat(sorted[i + 1].Value);
      if (qv > v1 && qv < v2) return v1;
    }
    return null;
  }

  function calcReductionAmount(calculationBy, basicRate, deduction) {
    var br = parseFloat(basicRate) || 0;
    var ded = parseFloat(deduction) || 0;
    if (calculationBy && calculationBy.toLowerCase() === 'percentage') return (br * ded / 100);
    return ded;
  }

  // ======================== RENDER DEDUCTION MATRIX TABLE (modal) ========================
  function renderDeductionMatrixTable(paramName, deductionMatrix, enteredValue) {
    if (!deductionMatrix || deductionMatrix.length === 0) {
      $('#dmTableWrapper').html('<p style="color:#aaa; font-size:11px;">No deduction matrix found.</p>');
      $('#dmParamName').text(paramName);
      $('#deductionMatrixSection').show();
      return;
    }
    var highlightVal = null, isExactMatch = false;
    if (enteredValue !== null && enteredValue !== undefined && enteredValue !== '') {
      var qv = parseFloat(enteredValue);
      if (!isNaN(qv)) {
        highlightVal = getMatchedMatrixValue(enteredValue, deductionMatrix);
        if (highlightVal !== null && highlightVal === qv) isExactMatch = true;
      }
    }
    var infoBanner = '';
    if (enteredValue !== null && enteredValue !== undefined && enteredValue !== '' && highlightVal !== null && !isExactMatch) {
      infoBanner = '<div style="margin-bottom:6px;padding:5px 8px;background:#fdf8e1;border:1px solid #f0ad4e;border-radius:4px;font-size:11px;color:#8a6d3b;">'
        + '<b><i class="fa fa-info-circle"></i> Closest match used:</b> Entered value <b>'
        + escHtml(String(enteredValue)) + '</b> &rarr; matched to matrix value <b>' + highlightVal + '</b>.</div>';
    }
    var html = '<table class="dm-table" style="width:100%;margin-top:4px;"><thead><tr><th>#</th><th>Value</th><th>Deduction</th></tr></thead><tbody>';
    deductionMatrix.forEach(function (dm, idx) {
      var isMatch = (highlightVal !== null && parseFloat(dm.Value) === highlightVal);
      html += '<tr class="' + (isMatch ? 'dm-highlight' : '') + '">'
        + '<td class="text-center">' + (idx + 1) + '</td>'
        + '<td>' + escHtml(dm.Value) + (isMatch ? ' &#8592;' : '') + '</td>'
        + '<td>' + escHtml(dm.Deduction) + '</td></tr>';
    });
    html += '</tbody></table>';
    $('#dmTableWrapper').html(infoBanner + html);
    $('#dmParamName').text(paramName);
    $('#deductionMatrixSection').show();
  }

  // ======================== REBUILD DEDUCTION MATRIX TABLE (page right side) ========================
  // ====== CHANGE 5: UOM replace for KG, Net Amount = Final Rate × Weight ======
  function rebuildDeductionMatrixTable() {
    var tbody = $('#deduction_matrix_tbody');
    tbody.empty();

    // ---- Bag Weight Sum ----
    var totalWeight = 0;
    var totalBagQty = 0;
    var totalfWeight = 0;
    var total = 0;
    $('#stack_qc_tbody tr').each(function () {
      var w = parseFloat($(this).find('input[name="weight[]"]').val()) || 0;
      var b = parseFloat($(this).find('input[name="bag_qty[]"]').val()) || 0;
      totalWeight += w;
      totalBagQty += b;
      totalfWeight += (b > 0 ? (w / b) : 0);
      total += totalfWeight * b;
    });
    $('#dm_bag_weight_val').text(
      (totalWeight > 0 || totalBagQty > 0) ? total.toFixed(2) + '' : '-'
    );

    var rowDataList = [];
    var hasAnyData = false;

    $.each(qcValuesStore, function (rowIdx, params) {
      var $row = $('#stack_qc_tbody tr[data-row="' + rowIdx + '"]');
      var itemID = $row.find('.sq-item-select').val() || '';
      var item = itemDataMap[itemID] || {};
      var itemName = item.item_name || itemID || ('Row ' + (parseInt(rowIdx) + 1));
      var basicRate = parseFloat(item.BasicRate) || 0;

      // ====== CHANGE 6: UOM per row ======
      var rowUom = getRowUom(rowIdx);

      // Row cha weight
      var rowWeight = parseFloat($row.find('input[name="weight[]"]').val()) || 0;

      var qcRows = modalQCData[rowIdx] || [];
      var paramIdMap = {};
      qcRows.forEach(function (r) { paramIdMap[String(r.id)] = r; });

      var rowParams = [];

      $.each(params, function (paramId, vals) {
        if (!vals.pct || vals.pct === '') return;
        hasAnyData = true;

        var paramName = vals.paramName || ('Param#' + paramId);
        var calcBy = vals.calcBy || '';
        var deduction = vals.deduction;
        var amount = (vals.reductionAmt !== null && vals.reductionAmt !== undefined)
          ? parseFloat(vals.reductionAmt) : 0;

        if ((deduction === null || deduction === undefined) || amount === 0) {
          var qcRowData = paramIdMap[String(paramId)] || null;
          if (qcRowData) {
            calcBy = qcRowData.CalculationBy || '';
            deduction = findDeduction(vals.pct, qcRowData.deduction_matrix);
            if (deduction !== null && basicRate > 0) {
              amount = calcReductionAmount(calcBy, basicRate, deduction);
            }
          }
        }

        rowParams.push({
          paramId: paramId,
          paramName: paramName,
          value: parseFloat(vals.pct) || 0,
          deduction: deduction,
          calcBy: calcBy,
          amount: amount || 0
        });
      });

      if (rowParams.length > 0) {
        rowDataList.push({
          rowIdx: parseInt(rowIdx),
          itemID: itemID,
          itemName: itemName,
          basicRate: basicRate,
          rowUom: rowUom,       // ====== CHANGE 7: UOM passed ======
          rowWeight: rowWeight, // ====== CHANGE 8: Weight for Net Amount calculation ======
          params: rowParams
        });
      }
    });

    var totalAllDeductions = 0;
    // ====== CHANGE 9: Net Amount = Sum of (Final Rate × Weight) per row ======
    var totalNetAmount = 0;

    if (!hasAnyData) {
      tbody.html('<tr style="text-align:center;color:#999;"><td colspan="4">Select items & enter QC values to see calculations...</td></tr>');
      $('#dm_total_deduction_val').text('-');
      $('#dm_net_amount_val').text('-');
      return;
    }

    rowDataList.forEach(function (rData) {
      // ====== CHANGE 10: UOM show in header instead of hardcoded KG ======
      tbody.append(
        '<tr class="dm-item-header-row"><td colspan="4">'
        + '<i class="fa fa-cube"></i> <b>' + escHtml(rData.itemName) + '</b>'
        + ' <small style="color:#95a5a6; font-weight:normal; margin-left:6px;">Row #' + (rData.rowIdx + 1)
        + ' &nbsp;|&nbsp; Base Rate: ₹' + parseFloat(rData.basicRate).toFixed(2)
        + ' / ' + escHtml(rData.rowUom)
        + ' &nbsp;|&nbsp; Weight: ' + parseFloat(rData.rowWeight).toFixed(2) + ' ' + escHtml(rData.rowUom)
        + '</small>'
        + '</td></tr>'
      );

      var rowTotalReduction = 0;

      rData.params.forEach(function (pData) {
        var amount = pData.amount || 0;
        var dedDisplay = '-';
        var calcByLabel = pData.calcBy ? ' (' + pData.calcBy + ')' : '';

        if (pData.deduction !== null && pData.deduction !== undefined) {
          dedDisplay = parseFloat(pData.deduction).toFixed(2)
            + ((pData.calcBy || '').toLowerCase() === 'percentage' ? '%' : '');
        }

        if (amount > 0) {
          rowTotalReduction += amount;
          totalAllDeductions += amount;
        }

        tbody.append(
          '<tr class="dm-param-row">'
          + '<td>' + escHtml(pData.paramName) + escHtml(calcByLabel) + '</td>'
          + '<td style="text-align:right;">' + parseFloat(pData.value).toFixed(2) + '</td>'
          + '<td style="text-align:right;"><span class="deduction-value">' + dedDisplay + '</span></td>'
          + '<td style="text-align:right;"><span class="amount-value">' + (amount > 0 ? amount.toFixed(2) : '-') + '</span></td>'
          + '</tr>'
        );
      });

      tbody.append(
        '<tr class="dm-item-total"><td colspan="2"><b>' + escHtml(rData.itemName) + ' - Total Deduction / ' + escHtml(rData.rowUom) + '</b></td>'
        + '<td colspan="2" style="text-align:right;"><b>₹' + rowTotalReduction.toFixed(2) + '</b></td></tr>'
      );

      var finalRate = rData.basicRate > 0 ? (rData.basicRate - rowTotalReduction) : 0;

      tbody.append(
        '<tr class="dm-final-rate-row"><td colspan="2"><b>Final Rate / ' + escHtml(rData.rowUom) + ' — ' + escHtml(rData.itemName) + '</b></td>'
        + '<td colspan="2" style="text-align:right;"><b>₹' + finalRate.toFixed(2) + '</b></td></tr>'
      );

      // ====== CHANGE 11: Net Amount = Final Rate × Row Weight ======
      var rowNetAmount = finalRate * rData.rowWeight;
      totalNetAmount += rowNetAmount;

      tbody.append(
        '<tr class="dm-summary-row">'
        + '<td colspan="2"><b>Net Amount — ' + escHtml(rData.itemName)
        + ' <small>(₹' + finalRate.toFixed(2) + ' × ' + parseFloat(rData.rowWeight).toFixed(2) + ' ' + escHtml(rData.rowUom) + ')</small></b></td>'
        + '<td colspan="2" style="text-align:right;"><b>₹' + rowNetAmount.toFixed(2) + '</b></td>'
        + '</tr>'
      );
    });

    $('#dm_total_deduction_val').text(totalAllDeductions.toFixed(2));
    // ====== CHANGE 12: Total Net Amount = sum of all rows ======
    $('#dm_net_amount_val').text('₹' + totalNetAmount.toFixed(2));
  }

  // ======================== RECALC MODAL ROW ========================
  function recalcModalRow($tr, qcRows, itemID) {
    var paramIdx = parseInt($tr.data('param-idx'));
    var qcRowData = qcRows[paramIdx];
    if (!qcRowData) return;

    var $qcInput = $tr.find('.qc-Percentage_Wise-input');
    var qcVal = $qcInput.val().trim();
    var item = itemDataMap[itemID] || {};
    var basicRate = parseFloat(item.BasicRate) || 0;
    var calcBy = qcRowData.CalculationBy || '';
    var paramName = qcRowData.ParameterName || '';

    var $matchedCell = $tr.find('.qc-matched-deduction');
    var $finalCell = $tr.find('.qc-final-amount');

    if (qcVal === '' || isNaN(parseFloat(qcVal))) {
      $matchedCell.html('<span style="color:#aaa;">-</span>');
      $finalCell.html('<span style="color:#aaa;">-</span>');
      renderDeductionMatrixTable(paramName, qcRowData.deduction_matrix, null);
      rebuildDeductionMatrixTable();
      return;
    }

    var currentVal = parseFloat(qcVal) || 0;
    var deductionOnVal = findDeduction(currentVal, qcRowData.deduction_matrix);

    if (deductionOnVal === null) {
      $matchedCell.html('<span style="color:#aaa;">-</span>');
      $finalCell.html('<span style="color:#aaa;">-</span>');
      renderDeductionMatrixTable(paramName, qcRowData.deduction_matrix, currentVal);
      rebuildDeductionMatrixTable();
      return;
    }

    var reductionAmt = calcReductionAmount(calcBy, basicRate, deductionOnVal);
    var dedDisplay = parseFloat(deductionOnVal).toFixed(2) + (calcBy.toLowerCase() === 'percentage' ? '%' : '');

    var matchedVal = getMatchedMatrixValue(currentVal, qcRowData.deduction_matrix);
    var isExact = (matchedVal !== null && parseFloat(matchedVal) === parseFloat(currentVal));
    var matchSuffix = isExact ? '' : ' <small style="color:#8a6d3b;">(~' + matchedVal + ')</small>';

    $matchedCell.html('<span class="qc-calc-badge"><i class="fa fa-arrow-down"></i> ' + escHtml(dedDisplay) + '</span>' + matchSuffix);
    $finalCell.html('<b>' + reductionAmt.toFixed(2) + '</b>');

    renderDeductionMatrixTable(paramName, qcRowData.deduction_matrix, currentVal);
    rebuildDeductionMatrixTable();
  }

  // ======================== SAVE CURRENT MODAL QC VALUES ========================
  function saveCurrentQCValues() {
    if (currentModalRowIndex === null) return;
    if (!qcValuesStore[currentModalRowIndex]) qcValuesStore[currentModalRowIndex] = {};

    var qcRows = modalQCData[currentModalRowIndex] || [];
    var item = itemDataMap[currentModalItemID] || {};
    var basicRate = parseFloat(item.BasicRate) || 0;

    $('#itemQCTableBody tr').each(function () {
      var $tr = $(this);
      var paramIdx = parseInt($tr.data('param-idx'));
      var qcRowData = qcRows[paramIdx];
      if (!qcRowData) return;

      var pctInput = $tr.find('.qc-Percentage_Wise-input');
      if (!pctInput.length) return;

      var nameAttr = pctInput.attr('name') || '';
      var paramName = pctInput.data('param-name') || '';
      var match = nameAttr.match(/\[(\w+)\]/);
      if (!match || !match[1]) return;

      var paramId = String(match[1]);
      var qcVal = pctInput.val();
      var deduction = findDeduction(qcVal, qcRowData.deduction_matrix);
      var calcBy = qcRowData.CalculationBy || '';
      var reductionAmt = (deduction !== null && basicRate > 0) ? calcReductionAmount(calcBy, basicRate, deduction) : null;

      qcValuesStore[currentModalRowIndex][paramId] = {
        pct: qcVal,
        paramName: paramName,
        parameter_id: paramId,
        deduction: deduction,
        calcBy: calcBy,
        reductionAmt: reductionAmt,
        basicRate: basicRate
      };
    });

    updateQCBadgesWithParamNames(currentModalRowIndex);
    rebuildDeductionMatrixTable();
  }

  // ======================== RESTORE QC VALUES IN MODAL ========================
  function restoreQCValues(rowIndex, qcRows) {
    var savedVals = qcValuesStore[rowIndex] || {};
    if (Object.keys(savedVals).length === 0) return;

    var paramIdToVal = {};
    $.each(savedVals, function (paramId, vals) {
      paramIdToVal[String(paramId)] = vals.pct || '';
    });

    var item = itemDataMap[currentModalItemID] || {};
    var basicRate = parseFloat(item.BasicRate) || 0;

    $('#itemQCTableBody tr').each(function () {
      var $tr = $(this);
      var pctInput = $tr.find('.qc-Percentage_Wise-input');
      if (!pctInput.length) return;

      var nameAttr = pctInput.attr('name') || '';
      var match = nameAttr.match(/\[(\w+)\]/);
      if (!match || !match[1]) return;

      var paramId = String(match[1]);
      if (paramIdToVal.hasOwnProperty(paramId) && paramIdToVal[paramId] !== '') {
        pctInput.val(paramIdToVal[paramId]);
        recalcModalRow($tr, qcRows, currentModalItemID);
      }
    });

    var freshStore = {};
    $('#itemQCTableBody tr').each(function () {
      var $tr = $(this);
      var paramIdx = parseInt($tr.data('param-idx'));
      var pctInput = $tr.find('.qc-Percentage_Wise-input');
      if (!pctInput.length) return;

      var nameAttr = pctInput.attr('name') || '';
      var match = nameAttr.match(/\[(\w+)\]/);
      if (!match || !match[1]) return;

      var paramId = String(match[1]);
      var paramName = pctInput.data('param-name') || '';
      var qcRowData = qcRows[paramIdx] || null;
      var qcVal = pctInput.val();
      var deduction = qcRowData ? findDeduction(qcVal, qcRowData.deduction_matrix) : null;
      var calcBy = qcRowData ? (qcRowData.CalculationBy || '') : '';
      var redAmt = (deduction !== null && basicRate > 0)
        ? calcReductionAmount(calcBy, basicRate, deduction)
        : (savedVals[paramId] ? (parseFloat(savedVals[paramId].reductionAmt) || null) : null);

      freshStore[paramId] = {
        pct: qcVal,
        paramName: paramName,
        parameter_id: paramId,
        deduction: deduction,
        calcBy: calcBy,
        reductionAmt: redAmt,
        basicRate: basicRate
      };
    });
    qcValuesStore[rowIndex] = freshStore;
  }

  // ======================== MODAL CLOSE — AUTO SAVE ========================
  $('#itemDetailModal').on('hide.bs.modal', function () {
    saveCurrentQCValues();
    currentModalRowIndex = null;
    currentModalItemID = null;
    $('#savedQcDisplay').hide();
    $('#savedQcText').text('');
    $('#deductionMatrixSection').hide();
  });

  // ======================== MODAL SAVE QC BUTTON ========================
  $(document).on('click', '#itemModalSaveQCBtn', function () {
    saveCurrentQCValues();
    toastr.success('QC values saved successfully!');
    $('#itemDetailModal').modal('hide');
  });

  // ======================== LIVE QC INPUT -> RECALCULATE ========================
  $(document).on('input change', '#itemQCTableBody .qc-Percentage_Wise-input', function () {
    var $tr = $(this).closest('tr');
    var qcRows = modalQCData[currentModalRowIndex] || [];
    recalcModalRow($tr, qcRows, currentModalItemID);
  });

  // ======================== OPEN ITEM DETAIL POPUP ========================
  function openItemDetailPopup(rowIndex, selectedItemID) {
    saveCurrentQCValues();
    if (!selectedItemID) { toastr.warning('Please select an item first!'); return; }
    var item = itemDataMap[selectedItemID];
    if (!item) { toastr.warning('Item details not found!'); return; }

    currentModalRowIndex = rowIndex;
    currentModalItemID = selectedItemID;

    // ====== CHANGE 13: Modal title madhe UOM show ======
    var rowUom = getRowUom(rowIndex);
    $('#itemModalTitle').text(item.item_name || selectedItemID);
    $('#itemModalBasicRate').text('Basic Rate: ₹' + (parseFloat(item.BasicRate) || 0).toFixed(2) + ' / ' + rowUom);

    var storedVals = qcValuesStore[rowIndex] || {};
    var savedQcParts = [];
    $.each(storedVals, function (paramId, vals) {
      if (vals.pct && vals.pct !== '') {
        savedQcParts.push((vals.paramName || ('Param#' + paramId)) + ': ' + vals.pct);
      }
    });
    if (savedQcParts.length > 0) {
      $('#savedQcText').text('[' + savedQcParts.join('], [') + ']');
      $('#savedQcDisplay').show();
    } else {
      $('#savedQcDisplay').hide();
      $('#savedQcText').text('');
    }

    $('#itemQCTableBody').html('<tr><td colspan="9" class="text-center" style="padding:8px !important;"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');
    $('#itemQCTableWrapper').hide();
    $('#itemQCLoader').show();
    $('#deductionMatrixSection').hide();
    $('#itemDetailModal').modal('show');

    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/ItemQCList'); ?>',
      type: 'POST',
      data: { ItemID: selectedItemID, '<?= $this->security->get_csrf_token_name(); ?>': csrfToken },
      dataType: 'json',
      success: function (res) {
        $('#itemQCLoader').hide();
        $('#itemQCTableWrapper').show();
        var rows = [];
        if (Array.isArray(res)) rows = res;
        else if (res.success == true && Array.isArray(res.data)) rows = res.data;
        else if (Array.isArray(res.data)) rows = res.data;
        modalQCData[rowIndex] = rows;

        if (rows.length > 0) {
          var qcHtml = '';
          rows.forEach(function (row, idx) {
            qcHtml += '<tr data-param-idx="' + idx + '">'
              + '<td class="text-center">' + (idx + 1) + '</td>'
              + '<td>' + escHtml(row.ParameterName || '-') + '</td>'
              + '<td>' + escHtml(row.MinValue || '-') + '</td>'
              + '<td>' + escHtml(row.MaxValue || '-') + '</td>'
              + '<td>' + escHtml(row.BaseValue || '-') + '</td>'
              + '<td><span style="font-size:10px;background:#e8f4fd;border-radius:3px;padding:1px 4px;">'
              + escHtml(row.CalculationBy || '-') + '</span></td>'
              + '<td><input type="text" step="any" class="form-control qc-Percentage_Wise-input qc-value-input"'
              + ' inputmode="decimal"'
              + ' name="qc_Percentage_Wise[' + escHtml(String(row.id)) + ']"'
              + ' data-param-name="' + escHtml(row.ParameterName || '') + '"'
              + ' data-param-idx="' + idx + '"'
              + ' placeholder="0.00"></td>'
              + '<td class="qc-matched-deduction"><span style="color:#aaa;">-</span></td>'
              + '<td class="qc-final-amount"><span style="color:#aaa;">-</span></td>'
              + '</tr>';
          });
          $('#itemQCTableBody').html(qcHtml);
          restoreQCValues(rowIndex, rows);
        } else {
          $('#itemQCTableBody').html('<tr><td colspan="9" class="text-center" style="padding:8px !important;color:#999;">No QC parameters found for this item.</td></tr>');
        }
      },
      error: function () {
        $('#itemQCLoader').hide();
        $('#itemQCTableWrapper').show();
        $('#itemQCTableBody').html('<tr><td colspan="9" class="text-center" style="padding:8px !important;color:red;">Failed to load QC parameters!</td></tr>');
      }
    });
  }

  // ======================== ITEM SELECT -> POPUP + WeightUnit update ========================
  $(document).on('change', '.sq-item-select', function () {
    var $row = $(this).closest('tr');
    var itemID = $(this).val();

    var item = itemDataMap[itemID] || {};
    var unitLabel = item.WeightUnit ? item.WeightUnit : 'KG';
    var unitWeight = item.UnitWeight ? item.UnitWeight : '';

    // ====== CHANGE 14: data-uom update when item changes ======
    $row.attr('data-uom', unitLabel);
    $row.find('.weight-unit-label').text(unitLabel);

    var $weightInput = $row.find('input[name="weight[]"]');
    if ($weightInput.val() === '' && unitWeight !== '') {
      $weightInput.val(unitWeight);
    }

    openItemDetailPopup($row.data('row'), itemID);
  });
  $(document).on('click', '.sq-item-info-btn', function () {
    var $row = $(this).closest('tr');
    openItemDetailPopup($row.data('row'), $row.find('.sq-item-select').val());
  });

  // ======================== BUILD ITEM DROPDOWN HTML ========================
  function buildItemDropdownHtml() {
    var html = '<select name="item_id[]" class="form-control item-select-dropdown sq-item-select" style="min-width:130px;">'
      + '<option value="" disabled selected>-- Select Item --</option>';
    itemDataList.forEach(function (itm) {
      html += '<option value="' + escHtml(itm.ItemID) + '" data-name="' + escHtml(itm.item_name) + '">' + escHtml(itm.item_name) + '</option>';
    });
    html += '</select>';
    return html;
  }

  // ======================== BUILD CHAMBER DROPDOWN HTML (JS) ========================
  function buildChamberDropdownHtml(selectedId) {
    selectedId = String(selectedId || '');
    var html = '<select name="chamber[]" class="form-control">'
      + '<option value="" disabled ' + (selectedId === '' ? 'selected' : '') + '>-- Select Chamber --</option>';
    chamberList.forEach(function (ch) {
      var isSelected = (String(ch.id) === selectedId) ? 'selected' : '';
      html += '<option value="' + escHtml(String(ch.id)) + '" ' + isSelected + '>' + escHtml(ch.ChamberName) + '</option>';
    });
    html += '</select>';
    return html;
  }

  // ======================== LIVE DATE TIME ========================
  function updateCurrentDateTime() {
    var now = new Date();
    var formatted = now.getFullYear() + '-'
      + ('0' + (now.getMonth() + 1)).slice(-2) + '-'
      + ('0' + now.getDate()).slice(-2) + ' '
      + ('0' + now.getHours()).slice(-2) + ':'
      + ('0' + now.getMinutes()).slice(-2) + ':'
      + ('0' + now.getSeconds()).slice(-2);
    var gwCell = document.getElementById('loadedDateTimeCell');
    if (gwCell && gwCell.getAttribute('data-saved') !== '1') gwCell.textContent = formatted;
    var twCell = document.getElementById('UploadedDateTimeCell');
    if (twCell && twCell.getAttribute('data-saved') !== '1') twCell.textContent = formatted;
    var cvCell = document.getElementById('cv_datetime_cell');
    if (cvCell && cvCell.getAttribute('data-saved') !== '1') cvCell.textContent = formatted;
  }
  updateCurrentDateTime();
  setInterval(updateCurrentDateTime, 1000);

  // ======================== IMAGE UPLOAD COLOR CHANGE ========================
  $(document).on('change', 'input[type="file"][accept="image/*"]', function () {
    if (this.files && this.files.length > 0) {
      var fileName = this.files[0].name;
      var shortName = fileName.length > 10 ? fileName.substring(0, 10) + '...' : fileName;
      $('label[for="' + this.id + '"]').removeClass('btn-default').addClass('btn-success uploaded').html('<i class="fa fa-check"></i> ' + shortName);
    }
  });

  // ======================== GROSS WEIGHT EDIT ========================
  $(document).on('click', '#gw_edit_btn', function () {
    $('#gw_weight_display').hide();
    $('#gross_weight').show().focus();
    switchToUpload('gw_top_image_cell', 'top_image', 'gw_top_image', 'existing_top_image');
    switchToUpload('gw_front_image_cell', 'front_image', 'gw_front_image', 'existing_front_image');
    switchToUpload('gw_side_image_cell', 'side_image', 'gw_side_image', 'existing_side_image');
    $(this).hide(); $('#gw_save_btn').show();
  });

  // ======================== TARE WEIGHT EDIT ========================
  $(document).on('click', '#tw_edit_btn', function () {
    $('#tw_weight_display').hide();
    $('#tare_weight').show().focus();
    switchToUpload('tw_top_image_cell', 'top_image', 'tw_top_image', 'existing_top_image');
    switchToUpload('tw_front_image_cell', 'front_image', 'tw_front_image', 'existing_front_image');
    switchToUpload('tw_side_image_cell', 'side_image', 'tw_side_image', 'existing_side_image');
    $(this).hide(); $('#tw_save_btn').show();
  });

  function switchToUpload(cellId, inputName, inputId, hiddenName) {
    $('#' + cellId).html(
      '<input type="file" name="' + inputName + '" id="' + inputId + '" accept="image/*" style="display:none;">'
      + '<label for="' + inputId + '" class="btn btn-xs btn-default mb0 upload-label">Upload</label>'
    );
  }

  // ======================== CONVEYOR EDIT ========================
  $(document).on('click', '#cv_edit_btn', function () {
    $('#conveyor_id').prop('disabled', false);
    $(this).hide(); $('#cv_save_btn').show();
  });

  // ======================== IMAGE MODAL ========================
  $(document).on('click', '#gross_weight_form a[target="_blank"], #tare_weight_form a[target="_blank"]', function (e) {
    e.preventDefault();
    $('#modalImage').attr('src', $(this).attr('href'));
    $('#imageModal').modal('show');
  });

  // ======================== CHAMBER DROPDOWN CHANGE ========================
  $(document).on('change', 'select[name="chamber[]"]', function () {
    var chamberID = $(this).val();
    var $row = $(this).closest('tr');
    $row.find('.stack-td').html('<select name="stack[]" class="form-control sq-stack-select"><option value="" disabled selected>-- Select Stack --</option></select>');
    $row.find('.lot-td').html('<select name="lot[]" class="form-control sq-lot-select"><option value="" disabled selected>-- Select Lot --</option></select>');
    if (!chamberID) return;
    loadStacksForRow($row, chamberID, '', '');
  });

  // ======================== STACK DROPDOWN CHANGE ========================
  $(document).on('change', '.sq-stack-select', function () {
    var stackID = $(this).val();
    var $row = $(this).closest('tr');
    $row.find('.lot-td').html('<select name="lot[]" class="form-control sq-lot-select"><option value="" disabled selected>-- Select Lot --</option></select>');
    if (!stackID) return;
    loadLotsForRow($row, stackID, '');
  });

  // ======================== STACK QC — ADD ROW ========================
  $(document).on('click', '.sq-add-row', function () {
    var tbody = $('#stack_qc_tbody');
    var rowCount = tbody.find('tr').length;
    var newIdx = rowCount;
    var newRow = '<tr data-row="' + newIdx + '" data-saved-chamber="" data-saved-stack="" data-saved-lot="" data-row-db-id="" data-uom="KG">'
      + '<td class="text-center row-num">' + (rowCount + 1) + '</td>'
      + '<td>' + buildItemDropdownHtml() + '</td>'
      + '<td>' + buildChamberDropdownHtml('') + '</td>'
      + '<td class="stack-td">' + buildStackDropdownHtml([], '') + '</td>'
      + '<td class="lot-td">' + buildLotDropdownHtml([], '') + '</td>'
      + '<td>'
      + '<div style="display:flex; align-items:center; gap:4px;">'
      + '<input type="text" name="weight[]" class="form-control" style="min-width:70px;">'
      + '<span class="weight-unit-label" style="font-size:10px; color:#31708f; background:#d9edf7; border:1px solid #5bc0de; border-radius:3px; padding:1px 5px; white-space:nowrap;">KG</span>'
      + '</div>'
      + '</td>'
      + '<td><input type="text" name="bag_qty[]" class="form-control"></td>'
      + '<td style="white-space:nowrap;">'
      + '<input type="text" class="form-control qc-display-input" name="qc[]" value="" data-saved-qc="" data-qc-json="[]">'
      + '<div class="qc-cell-inner">'
      + '<div class="qc-badge-wrapper" id="qc_badges_' + newIdx + '">'
      + '<span class="qc-badge-empty">-- No QC --</span>'
      + '</div>'
      + '<button type="button" class="btn btn-info btn-xs sq-item-info-btn" title="View / Fill QC Details" style="margin-left:3px;align-self:flex-start;"><i class="fa fa-info-circle"></i></button>'
      + '</div>'
      + '</td>'
      + '<td style="width:60px;text-align:center;">'
      + '<button type="button" class="btn btn-success btn-xs sq-add-row"    title="Add Row"><i class="fa fa-plus"></i></button>'
      + '<button type="button" class="btn btn-danger  btn-xs sq-remove-row" title="Remove Row"><i class="fa fa-minus"></i></button>'
      + '</td>'
      + '</tr>';
    tbody.append(newRow);
    renumberStackRows();
  });

  // ======================== STACK QC — REMOVE ROW ========================
  $(document).on('click', '.sq-remove-row', function () {
    var tbody = $('#stack_qc_tbody');
    if (tbody.find('tr').length <= 1) { toastr.warning('At least one row is required!'); return; }
    var removedIdx = parseInt($(this).closest('tr').data('row'));
    $(this).closest('tr').remove();
    delete qcValuesStore[removedIdx];
    delete modalQCData[removedIdx];
    renumberStackRows();
  });

  function renumberStackRows() {
    var newStore = {}, newModal = {};
    $('#stack_qc_tbody tr').each(function (i) {
      var oldIdx = parseInt($(this).data('row'));
      $(this).find('.row-num').text(i + 1);
      $(this).attr('data-row', i);
      $(this).find('.qc-badge-wrapper').attr('id', 'qc_badges_' + i);
      if (qcValuesStore[oldIdx] !== undefined) newStore[i] = qcValuesStore[oldIdx];
      if (modalQCData[oldIdx] !== undefined) newModal[i] = modalQCData[oldIdx];
    });
    qcValuesStore = newStore;
    modalQCData = newModal;
    $('#stack_qc_tbody tr').each(function () { updateQCBadgesWithParamNames(parseInt($(this).data('row'))); });
    rebuildDeductionMatrixTable();
  }

  // ======================== STACK QC FORM SUBMIT ========================
  $('#stack_qc_form').on('submit', function (e) {
    e.preventDefault();
    saveCurrentQCValues();

    var structuredRows = [];

    $('#stack_qc_tbody tr').each(function () {
      var $tr = $(this);
      var rowIdx = parseInt($tr.data('row'));
      var item_id = $tr.find('select[name="item_id[]"]').val() || '';
      var chamber = $tr.find('select[name="chamber[]"]').val() || '';
      var stack = $tr.find('select[name="stack[]"]').val() || '';
      var lot = $tr.find('select[name="lot[]"]').val() || '';
      var weight = $tr.find('input[name="weight[]"]').val() || '';
      var bag_qty = $tr.find('input[name="bag_qty[]"]').val() || '';
      // ====== CHANGE 15: UOM submit madhe include kara ======
      var uom = $tr.attr('data-uom') || $tr.find('.weight-unit-label').text() || 'KG';

      var qcParams = [];
      var storedQC = qcValuesStore[rowIdx] || {};

      $.each(storedQC, function (paramKey, vals) {
        if (vals.pct === '' || vals.pct === undefined || vals.pct === null) return;
        qcParams.push({
          parameter_id: vals.parameter_id || paramKey,
          value: vals.pct,
          deductionamt: (vals.reductionAmt !== null && vals.reductionAmt !== undefined)
            ? parseFloat(vals.reductionAmt).toFixed(2)
            : '0.00'
        });
      });

      structuredRows.push({ item_id, chamber, stack, lot, weight, bag_qty, uom, qc: qcParams });
    });

    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    var postData = {
      GateINID: $('#sq_GateINID').val(),
      form_mode: $('#sq_form_mode').val(),
      godown: $('#sq_gatein_id').val(),
      update_id: $('#sq_update_id').val(),
      rows_json: JSON.stringify(structuredRows)
    };
    postData['<?= $this->security->get_csrf_token_name(); ?>'] = csrfToken;

    $('#sq_update_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    $.ajax({
      url: '<?= admin_url('purchase/Inwards/SaveStackQCDetails'); ?>',
      type: 'POST',
      data: postData,
      dataType: 'json',
      success: function (res) {
        $('#sq_update_btn').prop('disabled', false).html('<i class="fa fa-save"></i> UPDATE STACK DETAILS');
        if (res.success == true) {
          toastr.success(res.message || 'Stack QC details saved successfully!');

          $('#sq_form_mode').val('edit');

          var resRows = [];
          if (Array.isArray(res.data)) resRows = res.data;
          else if (res.data && typeof res.data === 'object') resRows = [res.data];

          if (resRows.length > 0) {
            if (resRows[0] && resRows[0].id) {
              $('#sq_update_id').val(resRows[0].id);
            }

            var newQcValuesStore = {};

            resRows.forEach(function (resRow, ri) {
              var $tr = $('#stack_qc_tbody tr[data-row="' + ri + '"]');
              if (!$tr.length) return;

              $tr.attr('data-row-db-id', resRow.id || '');

              // ====== CHANGE 16: UOM response madhe asel tr update kara ======
              if (resRow.uom) {
                $tr.attr('data-uom', resRow.uom);
                $tr.find('.weight-unit-label').text(resRow.uom);
              }

              var newQcArr = Array.isArray(resRow.qc) ? resRow.qc : [];
              $tr.find('.qc-display-input').attr('data-qc-json', JSON.stringify(newQcArr));

              newQcValuesStore[ri] = {};
              var itemID = $tr.find('.sq-item-select').val() || '';
              var item = itemDataMap[itemID] || {};
              var basicRate = parseFloat(item.BasicRate) || 0;
              var qcRows = modalQCData[ri] || [];

              var paramIdMap = {};
              qcRows.forEach(function (r) { paramIdMap[String(r.id)] = r; });

              newQcArr.forEach(function (qcEntry) {
                qcEntry = (typeof qcEntry === 'object') ? qcEntry : {};
                var paramId = String(qcEntry.parameter_id || '');
                var paramVal = String(qcEntry.value || '');
                var dedAmt = parseFloat(qcEntry.deductionamt) || 0;
                if (!paramId) return;

                var qcRowData = paramIdMap[paramId] || null;
                var paramName = qcRowData ? (qcRowData.ParameterName || '') : ('Param#' + paramId);
                var calcBy = qcRowData ? (qcRowData.CalculationBy || '') : '';
                var deduction = qcRowData ? findDeduction(paramVal, qcRowData.deduction_matrix) : null;
                var reductionAmt = (deduction !== null && basicRate > 0)
                  ? calcReductionAmount(calcBy, basicRate, deduction)
                  : dedAmt;

                newQcValuesStore[ri][paramId] = {
                  pct: paramVal,
                  paramName: paramName,
                  parameter_id: paramId,
                  deduction: deduction,
                  calcBy: calcBy,
                  reductionAmt: reductionAmt,
                  basicRate: basicRate
                };
              });

              updateQCBadgesWithParamNames(ri);
            });

            $.each(qcValuesStore, function (ri, vals) {
              if (newQcValuesStore[ri] === undefined) {
                newQcValuesStore[ri] = vals;
              }
            });
            qcValuesStore = newQcValuesStore;
          }

          rebuildDeductionMatrixTable();

        } else {
          toastr.error(res.message || 'Failed to save Stack QC details!');
        }
      },
      error: function () {
        $('#sq_update_btn').prop('disabled', false).html('<i class="fa fa-save"></i> UPDATE STACK DETAILS');
        toastr.error('Something went wrong!');
      }
    });
  });

  // ======================== GROSS WEIGHT SUBMIT ========================
  $('#gross_weight_form').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/SaveGrossWeight'); ?>',
      type: 'POST', data: formData, dataType: 'json', processData: false, contentType: false,
      success: function (res) {
        if (res.success == true) {
          toastr.success(res.message || 'Gross Weight saved successfully!');
          $('#gw_loaded_by').html(res.data.UserID || '');
          if (res.data.value) {
            if (res.data.value.TopImage) $('#gw_top_image_cell').html('<a href="' + getAbsUrl(res.data.value.TopImage) + '" target="_blank">View</a><input type="hidden" name="existing_top_image" value="' + res.data.value.TopImage + '">');
            if (res.data.value.FrontImage) $('#gw_front_image_cell').html('<a href="' + getAbsUrl(res.data.value.FrontImage) + '" target="_blank">View</a><input type="hidden" name="existing_front_image" value="' + res.data.value.FrontImage + '">');
            if (res.data.value.SideImage) $('#gw_side_image_cell').html('<a href="' + getAbsUrl(res.data.value.SideImage) + '" target="_blank">View</a><input type="hidden" name="existing_side_image" value="' + res.data.value.SideImage + '">');
          }
          $('#gw_update_id').val(res.data.id);
          $('#gw_form_mode').val('edit');
          var gwVal = (res.data.value && res.data.value.gross_weight) ? res.data.value.gross_weight : $('#gross_weight').val();
          $('#gross_weight').val(gwVal).hide();
          $('#gw_weight_display').text(gwVal).show();
          if (res.data.TransDate) $('#loadedDateTimeCell').text(res.data.TransDate).attr('data-saved', '1');
          $('#gw_save_btn').hide();
          if ($('#gw_edit_btn').length === 0) $('#gw_save_btn').after('<button type="button" class="btn btn-warning btn-xs" id="gw_edit_btn" title="Edit"><i class="fa fa-pencil"></i></button>');
          else $('#gw_edit_btn').show();
          calculateActualWeight();
        } else { toastr.error(res.message || 'Failed to save Gross Weight!'); }
      },
      error: function () { toastr.error('Something went wrong!'); }
    });
  });

  // ======================== TARE WEIGHT SUBMIT ========================
  $('#tare_weight_form').on('submit', function (e) {
    e.preventDefault();
    var grossVal = parseFloat($('#gw_weight_display').text());
    if (isNaN(grossVal) || grossVal === 0) grossVal = parseFloat($('#gross_weight').val()) || 0;
    var tareVal = parseFloat($('#tare_weight').val()) || 0;
    if (grossVal === 0) { toastr.warning('Please enter Gross Weight first before saving Tare Weight.'); return; }
    if (tareVal <= 0) { toastr.warning('Please enter a valid Tare Weight.'); return; }
    if (tareVal >= grossVal) {
      toastr.error('Tare Weight (' + tareVal + ' KG) cannot be greater than or equal to Gross Weight (' + grossVal + ' KG).');
      $('#tare_weight').focus().css('border-color', '#e74c3c');
      return;
    }
    $('#tare_weight').css('border-color', '');
    var formData = new FormData(this);
    formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/SaveTareWeight'); ?>',
      type: 'POST', data: formData, dataType: 'json', processData: false, contentType: false,
      success: function (res) {
        if (res.success == true) {
          toastr.success(res.message || 'Tare Weight saved successfully!');
          $('#tw_uploaded_by').html(res.data.UserID || '');
          if (res.data.value) {
            if (res.data.value.TopImage) $('#tw_top_image_cell').html('<a href="' + getAbsUrl(res.data.value.TopImage) + '" target="_blank">View</a><input type="hidden" name="existing_top_image" value="' + res.data.value.TopImage + '">');
            if (res.data.value.FrontImage) $('#tw_front_image_cell').html('<a href="' + getAbsUrl(res.data.value.FrontImage) + '" target="_blank">View</a><input type="hidden" name="existing_front_image" value="' + res.data.value.FrontImage + '">');
            if (res.data.value.SideImage) $('#tw_side_image_cell').html('<a href="' + getAbsUrl(res.data.value.SideImage) + '" target="_blank">View</a><input type="hidden" name="existing_side_image" value="' + res.data.value.SideImage + '">');
          }
          $('#tw_update_id').val(res.data.id);
          $('#tw_form_mode').val('edit');
          var twVal = (res.data.value && res.data.value.tare_weight) ? res.data.value.tare_weight : $('#tare_weight').val();
          $('#tare_weight').val(twVal).hide();
          $('#tw_weight_display').text(twVal).show();
          if (res.data.TransDate) $('#UploadedDateTimeCell').text(res.data.TransDate).attr('data-saved', '1');
          $('#tw_save_btn').hide();
          if ($('#tw_edit_btn').length === 0) $('#tw_save_btn').after('<button type="button" class="btn btn-warning btn-xs" id="tw_edit_btn" title="Edit"><i class="fa fa-pencil"></i></button>');
          else $('#tw_edit_btn').show();
          calculateActualWeight();
        } else { toastr.error(res.message || 'Failed to save Tare Weight!'); }
      },
      error: function () { toastr.error('Something went wrong!'); }
    });
  });

  // ======================== CONVEYOR SUBMIT ========================
  $('#conveyor_form').on('submit', function (e) {
    e.preventDefault();
    var conveyorVal = $('#conveyor_id').val();
    if (!conveyorVal) { toastr.warning('Please select a conveyor!'); return; }
    var formData = new FormData(this);
    formData.append('conveyor_id', conveyorVal);
    formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/SaveConveyorAssignment'); ?>',
      type: 'POST', data: formData, dataType: 'json', processData: false, contentType: false,
      success: function (res) {
        if (res.success == true) {
          toastr.success(res.message || 'Conveyor saved successfully!');
          $('#cv_added_by').html(res.data.UserID || '');
          $('#cv_update_id').val(res.data.id);
          $('#cv_form_mode').val('edit');
          var cvVal = (res.data.value && res.data.value.ConveyorID) ? res.data.value.ConveyorID : conveyorVal;
          $('#conveyor_id').val(cvVal).prop('disabled', true);
          if (res.data.TransDate) $('#cv_datetime_cell').text(res.data.TransDate).attr('data-saved', '1');
          $('#cv_save_btn').hide();
          if ($('#cv_edit_btn').length === 0) $('#cv_save_btn').after('<button type="button" class="btn btn-warning btn-xs" id="cv_edit_btn" title="Edit"><i class="fa fa-pencil"></i></button>');
          else $('#cv_edit_btn').show();
        } else { toastr.error(res.message || 'Failed to save conveyor!'); }
      },
      error: function () { toastr.error('Something went wrong!'); }
    });
  });

  // ======================== GATE OUT PASS ========================
  $(document).on('click', '#gateout_pass_btn', function () {
    var GateINID = '<?= $gatein->GateINID; ?>';
    if (!GateINID) { toastr.warning('GateINID not found!'); return; }
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/SaveGateOutPass'); ?>',
      type: 'POST', data: { GateINID: GateINID, '<?= $this->security->get_csrf_token_name(); ?>': csrfToken }, dataType: 'json',
      success: function (res) {
        if (res.success == true) {
          toastr.success(res.message || 'Gate Out Pass saved successfully!');
          $('#gateout_by_cell').text(res.data.UserID || '-');
          $('#gateout_datetime_cell').text((res.data.value && res.data.value.Time) ? res.data.value.Time : (res.data.TransDate || '-'));
          $btn.prop('disabled', true).html('<i class="fa fa-check"></i> Gate Out Done');
        } else {
          toastr.error(res.message || 'Failed to save Gate Out Pass!');
          $btn.prop('disabled', false).html('<i class="fa fa-sign-out"></i> GATE OUT PASS');
        }
      },
      error: function () { toastr.error('Something went wrong!'); $btn.prop('disabled', false).html('<i class="fa fa-sign-out"></i> GATE OUT PASS'); }
    });
  });

  // ======================== EXIT MARKED ========================
  $(document).on('click', '#exit_marked_btn', function () {
    var GateINID = '<?= $gatein->GateINID; ?>';
    if (!GateINID) { toastr.warning('GateINID not found!'); return; }
    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/SaveGateExit'); ?>',
      type: 'POST', data: { GateINID: GateINID, '<?= $this->security->get_csrf_token_name(); ?>': csrfToken }, dataType: 'json',
      success: function (res) {
        if (res.success == true) {
          toastr.success(res.message || 'Exit marked successfully!');
          $('#exit_by_cell').text(res.data.UserID || '-');
          $('#exit_datetime_cell').text((res.data.value && res.data.value.Time) ? res.data.value.Time : (res.data.TransDate || '-'));
          $btn.prop('disabled', true).html('<i class="fa fa-check"></i> Exit Done');
        } else {
          toastr.error(res.message || 'Failed to mark Exit!');
          $btn.prop('disabled', false).html('<i class="fa fa-sign-out"></i> EXIT MARKED');
        }
      },
      error: function () { toastr.error('Something went wrong!'); $btn.prop('disabled', false).html('<i class="fa fa-sign-out"></i> EXIT MARKED'); }
    });
  });

  // ======================== ADVANCE PAYMENT ========================
  $(document).on('click', '#advance_payment_btn', function () {
    var GateINID = $('#sq_GateINID').val() || '<?= $gatein->GateINID; ?>';
    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    var actualWeight = $('#actual_weight_display').text().trim();
    var bagWeight = $('#dm_bag_weight_val').text().trim();
    var totalDeduction = $('#dm_total_deduction_val').text().trim();
    var netAmt = $('#dm_net_amount_val').text().trim();

    var deductionArr = [];
    var finalRateArr = [];
    var qcMatrix = [];

    $('#deduction_matrix_tbody tr').each(function () {
      var $tr = $(this);
      if ($tr.hasClass('dm-item-header-row')) return;
      if ($tr.hasClass('dm-item-total')) {
        var itemTotal = $tr.find('td').eq(2).text().trim();
        if (itemTotal && itemTotal !== '-') deductionArr.push({ name: 'Item Deduction', amount: itemTotal });
        return;
      }
      if ($tr.hasClass('dm-final-rate-row')) {
        var itemName = $tr.find('td').eq(0).text().trim();
        var rate = $tr.find('td').eq(2).text().trim();
        if (itemName && rate && rate !== '-') finalRateArr.push({ name: itemName.replace(/Final Rate \/ \w+ — /, ''), value: rate });
        return;
      }
      if ($tr.hasClass('dm-param-row')) {
        var paramName = $tr.find('td').eq(0).text().trim();
        var amount = $tr.find('td').eq(3).text().trim();
        if (paramName && amount && amount !== '-') deductionArr.push({ name: paramName, amount: amount });
      }
    });

    $.each(qcValuesStore, function (rowIdx, params) {
      var $row = $('#stack_qc_tbody tr[data-row="' + rowIdx + '"]');
      var itemID = $row.find('.sq-item-select').val() || '';
      var item = itemDataMap[itemID] || {};
      $.each(params, function (paramId, vals) {
        if (vals.pct !== '' && vals.pct !== undefined) {
          qcMatrix.push({
            item_id: itemID,
            item_name: item.item_name || '',
            parameter_id: vals.parameter_id || paramId,
            param_name: vals.paramName || '',
            qc_value: vals.pct,
            deduction: vals.deduction !== null ? vals.deduction : '',
            calc_by: vals.calcBy || '',
            reduction: (vals.reductionAmt !== null && vals.reductionAmt !== undefined)
              ? parseFloat(vals.reductionAmt).toFixed(2) : ''
          });
        }
      });
    });

    if (!GateINID) { toastr.warning('GateINID not found!'); return; }
    if (deductionArr.length === 0 && finalRateArr.length === 0) {
      toastr.warning('No deduction data found. Please fill QC values first.');
      return;
    }

    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    var postData = {
      GateINID: GateINID,
      ActualWeight: actualWeight,
      TotalDeduction: totalDeduction,
      NetAmt: netAmt,
      BagWeight: bagWeight,
      Deduction: JSON.stringify(deductionArr),
      FinalRate: JSON.stringify(finalRateArr),
      QCMatrix: JSON.stringify(qcMatrix)
    };
    postData['<?= $this->security->get_csrf_token_name(); ?>'] = csrfToken;

    $.ajax({
      url: '<?= admin_url('purchase/Inwards/SaveDeductionMatrix'); ?>',
      type: 'POST', data: postData, dataType: 'json',
      success: function (res) {
        $btn.prop('disabled', false).html('<i class="fa fa-money"></i> ADVANCE PAYMENT');
        if (res.success == true) {
          toastr.success(res.message || 'Deduction Matrix saved successfully!');
        } else {
          toastr.error(res.message || 'Failed to save Deduction Matrix!');
        }
      },
      error: function () {
        $btn.prop('disabled', false).html('<i class="fa fa-money"></i> ADVANCE PAYMENT');
        toastr.error('Something went wrong!');
      }
    });
  });

  // ======================== LIVE WEIGHT / BAG QTY → REBUILD ========================
  $(document).on('input change', 'input[name="weight[]"], input[name="bag_qty[]"]', function () {
    rebuildDeductionMatrixTable();
  });
</script>