<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
  table { border-collapse: collapse; width: 100%; }
  th, td { padding: 1px 5px !important; white-space: nowrap; border: 1px solid !important; font-size: 11px; line-height: 1.42857143 !important; vertical-align: middle !important; }
  th { background: #50607b; color: #fff !important; }
  .sortable { cursor: pointer; }

  .upload-label {
    transition: background-color 0.3s, color 0.3s, border-color 0.3s;
    cursor: pointer;
  }
  .upload-label.uploaded {
    background-color: #5cb85c !important;
    border-color: #4cae4c !important;
    color: #fff !important;
  }
  .upload-label.uploaded i { margin-right: 3px; }

  #itemDetailModal .modal-header { background: #50607b; color: #fff; padding: 10px 15px; }
  #itemDetailModal .modal-header .close { color: #fff; opacity: 1; }
  #itemDetailModal .modal-body { padding: 15px; }
  #itemDetailModal table th { background: #50607b; color: #fff !important; font-size: 12px; }
  #itemDetailModal table td { font-size: 12px; }
  .item-select-dropdown { min-width: 130px; }

  .qc-display-input { display: none !important; }

  .qc-badge-wrapper { display: inline-flex; flex-direction: column; gap: 2px; vertical-align: middle; max-width: 180px; }
  .qc-badge { display: block; background-color: #dff0d8; border: 1px solid #5cb85c; color: #3c763d; border-radius: 3px; padding: 1px 6px; font-size: 10px; font-weight: bold; white-space: nowrap; }
  .qc-badge-empty { color: #aaa; font-size: 10px; font-style: italic; }
  .qc-cell-inner { display: inline-flex; align-items: flex-start; gap: 4px; }

  .actual-weight-value { font-weight: bold; color: #3c763d; }

  .heading-with-btn { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
  .heading-with-btn h4 { margin: 0 !important; }

  /* ====== DEDUCTION MATRIX MODAL TABLE ====== */
  #deductionMatrixSection { margin-top: 15px; border-top: 2px solid #50607b; padding-top: 10px; }
  #deductionMatrixSection h6 { color: #50607b; font-weight: bold; margin-bottom: 6px; }
  .dm-table thead th { background: #50607b; color: #fff !important; font-size: 11px; }
  .dm-table td { font-size: 11px; padding: 2px 5px !important; }
  .dm-highlight { background-color: #fcf8e3 !important; font-weight: bold; color: #8a6d3b; }
  .dm-result-row td { background-color: #dff0d8 !important; font-weight: bold; color: #3c763d; font-size: 12px; }
  .qc-input-with-calc { display: flex; align-items: center; gap: 4px; }
  .qc-value-input { min-width: 90px; }
  .qc-calc-badge { background: #d9edf7; border: 1px solid #5bc0de; color: #31708f; border-radius: 3px; padding: 1px 5px; font-size: 10px; white-space: nowrap; }

  /* Summary deduction table (page level) */
  #deduction_matrix_body td { font-size: 11px; }
  .deduction-param-val { font-weight: bold; color: #c0392b; }

  /* ====== SAVED DEDUCTION ROWS (from gatein) ====== */
  .dm-saved-deduction-row td { background-color: #fdf8e1 !important; }
  .dm-saved-final-rate-row td { background-color: #dff0d8 !important; font-weight: bold; color: #3c763d; }
  .dm-saved-net-amount-row td { background-color: #d9edf7 !important; font-weight: bold; color: #31708f; font-size: 12px; }
</style>

<?php
$gw = !empty($gross_weight) ? (object)$gross_weight : null;
if ($gw && isset($gw->value) && is_array($gw->value)) { $gw->value = (object)$gw->value; }
$tw = !empty($tare_weight) ? (object)$tare_weight : null;
if ($tw && isset($tw->value) && is_array($tw->value)) { $tw->value = (object)$tw->value; }
$cv = null;
if (!empty($conveyor)) {
  $cv = (object)$conveyor;
  if ($cv && isset($cv->value)) {
    if (is_array($cv->value)) { $cv->value = (object)$cv->value; }
    if (isset($cv->value->ConveyorID)) { $cv->value->conveyor_id = $cv->value->ConveyorID; }
  }
}

$itemList = [];
if (!empty($inward['history']) && is_array($inward['history'])) {
    foreach ($inward['history'] as $histRow) {
        $itemList[] = [
            'ItemID'      => $histRow['ItemID']      ?? '',
            'item_name'   => $histRow['item_name']   ?? '',
            'BasicRate'   => $histRow['BasicRate']   ?? '',
            'SaleRate'    => $histRow['SaleRate']    ?? '',
            'UnitWeight'  => $histRow['UnitWeight']  ?? '',
            'WeightUnit'  => $histRow['WeightUnit']  ?? '',
            'OrderQty'    => $histRow['OrderQty']    ?? '',
            'SuppliedIn'  => $histRow['SuppliedIn']  ?? '',
            'OrderAmt'    => $histRow['OrderAmt']    ?? '',
            'NetOrderAmt' => $histRow['NetOrderAmt'] ?? '',
            'igst'        => $histRow['igst']        ?? '',
            'igstamt'     => $histRow['igstamt']     ?? '',
            'cgst'        => $histRow['cgst']        ?? '',
            'sgst'        => $histRow['sgst']        ?? '',
            'batch_no'    => $histRow['batch_no']    ?? '',
            'expiry_date' => $histRow['expiry_date'] ?? '',
        ];
    }
}

$sqRows = [];
if (!empty($stack_qc)) {
    $sqVal = is_array($stack_qc) ? ($stack_qc['value'] ?? []) : ($stack_qc->value ?? []);
    $sqRows = is_array($sqVal) ? $sqVal : (array)$sqVal;
}

$qcValuesStorePhp = [];
foreach ($sqRows as $ri => $sqRow) {
    $sqRow = is_object($sqRow) ? $sqRow : (object)$sqRow;
    $qcStr = $sqRow->qc ?? '';
    if (!empty($qcStr)) {
        preg_match_all('/\[([^\]:]+):\s*([^\]]*)\]/', $qcStr, $matches, PREG_SET_ORDER);
        foreach ($matches as $idx => $match) {
            $paramName = trim($match[1]);
            $paramVal  = trim($match[2]);
            $fakeParamId = 'saved_' . $ri . '_' . $idx;
            $qcValuesStorePhp[$ri][$fakeParamId] = ['pct' => $paramVal, 'paramName' => $paramName];
        }
    }
}

$phpGrossWeight  = isset($gw->value->gross_weight) ? (float)$gw->value->gross_weight : 0;
$phpTareWeight   = isset($tw->value->tare_weight)  ? (float)$tw->value->tare_weight  : 0;
$phpActualWeight = ($phpGrossWeight > 0 && $phpTareWeight > 0) ? ($phpGrossWeight - $phpTareWeight) : null;

// ====== PARSE SAVED GATEIN DEDUCTION DATA ======
$savedDeductions = [];
$savedFinalRates = [];
$savedNetAmt     = '';
$savedBagWeight  = '';
$savedTotalDeduction = '';
$savedActualWeight   = '';

if (!empty($gatein->Deduction)) {
    $dRaw = $gatein->Deduction;
    // Strip outer quotes if double-encoded JSON
    if (is_string($dRaw) && strlen($dRaw) > 0 && $dRaw[0] === '"') {
        $dRaw = json_decode($dRaw); // first decode strips outer string
    }
    $decoded = json_decode($dRaw, true);
    if (is_array($decoded)) $savedDeductions = $decoded;
}

if (!empty($gatein->FinalRate)) {
    $fRaw = $gatein->FinalRate;
    if (is_string($fRaw) && strlen($fRaw) > 0 && $fRaw[0] === '"') {
        $fRaw = json_decode($fRaw);
    }
    $decoded = json_decode($fRaw, true);
    if (is_array($decoded)) $savedFinalRates = $decoded;
}

if (!empty($gatein->NetAmt))          $savedNetAmt          = $gatein->NetAmt;
if (!empty($gatein->BagWeight))       $savedBagWeight       = $gatein->BagWeight;
if (!empty($gatein->TotalDeduction))  $savedTotalDeduction  = $gatein->TotalDeduction;
if (!empty($gatein->ActualWeight))    $savedActualWeight    = $gatein->ActualWeight;
?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-10">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color: #fff !important; margin-bottom: 0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
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
              <!-- <pre><?= print_r($gatein);?> -->
              </pre>
              <div class="col-md-12 mbot5">
                <table>
                  <tbody>
                    <tr>
                      <td><b>Account ID : </b></td><td><?= $inward['AccountID'] ?? '-'; ?></td>
                      <td><b>Party Name : </b></td><td><?= $inward['company'] ?? '-'; ?></td>
                    </tr>
                    <tr>
                      <td><b>Order ID : </b></td><td><b><?= $inward['OrderID'] ?? '-'; ?></b></td>
                      <td><b>Party Type : </b></td><td>-</td>
                    </tr>
                    <tr>
                      <td><b>ASN By : </b></td><td>-</td>
                      <td><b>ASN Date: </b></td><td>-</td>
                    </tr>
                    <tr>
                      <td><b>ASN : </b></td>
                      <td><a href="" target="_blank">View ASN</a></td>
                      <td><b>Gate In Pass : </b></td>
                      <td><a href="<?= admin_url('purchase/Vehiclein/GateinPassPrint/' . $gatein->GateINID); ?>" target="_blank">View Gate In Pass</a></td>
                    </tr>
                    <tr>
                      <td><b>ASN Quantity(MT): </b></td><td>-</td>
                      <td><b>ASN Quantity(Bag): </b></td><td>-</td>
                    </tr>
                    <tr>
                      <td><b>Gate In By : </b></td><td><?= $gatein->UserID ?? '-'; ?></td>
                      <td><b>Gate In Date : </b></td><td><?= date('d/m/Y', strtotime($gatein->TransDate ?? '0000-00-00')); ?></td>
                    </tr>
                    <tr>
                      <td><b>Trade Rate (MT) : </b></td><td>-</td>
                      <td><b>Vehicle No. : </b></td><td><?= $gatein->VehicleNo ?? '-'; ?></td>
                    </tr>
                    <tr>
                      <td><b>Center Name : </b></td><td><?= $gatein->LocationName ?? '-'; ?></td>
                      <td><b>Status : </b></td><td><?= $gatein->status ?? '-'; ?></td>
                    </tr>
                    <tr>
                      <td><b>Party Invoice : </b></td>
                      <td><a href="" target="_blank">Click to View Party Invoice</a></td>
                      <td></td><td></td>
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
                  <input type="hidden" name="GateINID"  id="gw_GateINID"  value="<?= $gatein->GateINID; ?>">
                  <input type="hidden" name="form_mode" id="gw_form_mode" value="<?= $gw ? 'edit' : 'add'; ?>">
                  <table>
                    <tbody>
                      <tr>
                        <th>Total Weight(MT)</th><th>Top Image</th><th>Front Image</th><th>Side Image</th>
                        <th>Loaded By</th><th>Loaded Date Time</th><th>Action</th>
                      </tr>
                      <tr>
                        <td>
                          <?php if ($gw && isset($gw->value->gross_weight)) : ?>
                            <span id="gw_weight_display"><?= $gw->value->gross_weight; ?></span>
                            <input type="text" name="gross_weight" id="gross_weight" class="form-control" value="<?= $gw->value->gross_weight; ?>" style="display:none;" required>
                          <?php else : ?>
                            <span id="gw_weight_display" style="display:none;"></span>
                            <input type="text" name="gross_weight" id="gross_weight" class="form-control" required>
                          <?php endif; ?>
                        </td>
                        <td id="gw_top_image_cell">
                          <?php if ($gw && !empty($gw->value->TopImage)) : ?>
                            <a href="<?= base_url($gw->value->TopImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_top_image" value="<?= $gw->value->TopImage; ?>">
                          <?php else : ?>
                            <input type="file" name="top_image" id="gw_top_image" accept="image/*" style="display:none;">
                            <label for="gw_top_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="gw_front_image_cell">
                          <?php if ($gw && !empty($gw->value->FrontImage)) : ?>
                            <a href="<?= base_url($gw->value->FrontImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_front_image" value="<?= $gw->value->FrontImage; ?>">
                          <?php else : ?>
                            <input type="file" name="front_image" id="gw_front_image" accept="image/*" style="display:none;">
                            <label for="gw_front_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="gw_side_image_cell">
                          <?php if ($gw && !empty($gw->value->SideImage)) : ?>
                            <a href="<?= base_url($gw->value->SideImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_side_image" value="<?= $gw->value->SideImage; ?>">
                          <?php else : ?>
                            <input type="file" name="side_image" id="gw_side_image" accept="image/*" style="display:none;">
                            <label for="gw_side_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="gw_loaded_by"><?= $gw->UserID ?? '-'; ?></td>
                        <td>
                          <?php if ($gw && !empty($gw->TransDate)) : ?>
                            <span id="loadedDateTimeCell" data-saved="1"><?= date('Y-m-d H:i:s', strtotime($gw->TransDate)); ?></span>
                          <?php else : ?>
                            <span id="loadedDateTimeCell"></span>
                          <?php endif; ?>
                        </td>
                        <td style="width:80px;">
                          <?php if ($gw) : ?>
                            <button type="button" class="btn btn-warning btn-xs" id="gw_edit_btn" title="Edit"><i class="fa fa-pencil"></i></button>
                            <button type="submit" class="btn btn-success btn-xs" id="gw_save_btn" style="display:none;" title="Save"><i class="fa fa-save"></i></button>
                          <?php else : ?>
                            <button type="button" class="btn btn-warning btn-xs" id="gw_edit_btn" title="Edit" style="display:none;"><i class="fa fa-pencil"></i></button>
                            <button type="submit" class="btn btn-success btn-xs" id="gw_save_btn" title="Save"><i class="fa fa-save"></i></button>
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
                      <input type="hidden" name="GateINID"  id="cv_GateINID"  value="<?= $gatein->GateINID; ?>">
                      <input type="hidden" name="form_mode" id="cv_form_mode" value="<?= $cv ? 'edit' : 'add'; ?>">
                      <table>
                        <tbody>
                          <tr>
                            <th>Conveyor</th><th>Added By</th><th>Date Time</th><th>Action</th>
                          </tr>
                          <tr>
                            <td>
                              <select name="conveyor_id" id="conveyor_id" class="form-control" <?= ($cv && !empty($cv->value->conveyor_id)) ? 'disabled' : ''; ?>>
                                <option value="" disabled <?= (!$cv || empty($cv->value->conveyor_id)) ? 'selected' : ''; ?>>None selected</option>
                                <?php foreach (['1','2','3','4'] as $c) : ?>
                                  <option value="<?= $c; ?>" <?= ($cv && !empty($cv->value->conveyor_id) && $cv->value->conveyor_id == $c) ? 'selected' : ''; ?>>Conveyor <?= $c; ?></option>
                                <?php endforeach; ?>
                              </select>
                            </td>
                            <td id="cv_added_by"><?= $cv->UserID ?? '-'; ?></td>
                            <td>
                              <?php if ($cv && !empty($cv->TransDate)) : ?>
                                <span id="cv_datetime_cell" data-saved="1"><?= date('Y-m-d H:i:s', strtotime($cv->TransDate)); ?></span>
                              <?php else : ?>
                                <span id="cv_datetime_cell"></span>
                              <?php endif; ?>
                            </td>
                            <td style="width:80px;">
                              <?php if ($cv) : ?>
                                <button type="button" class="btn btn-warning btn-xs" id="cv_edit_btn" title="Edit"><i class="fa fa-pencil"></i></button>
                                <button type="submit" class="btn btn-success btn-xs" id="cv_save_btn" style="display:none;" title="Save"><i class="fa fa-save"></i></button>
                              <?php else : ?>
                                <button type="button" class="btn btn-warning btn-xs" id="cv_edit_btn" title="Edit" style="display:none;"><i class="fa fa-pencil"></i></button>
                                <button type="submit" class="btn btn-success btn-xs" id="cv_save_btn" title="Save"><i class="fa fa-save"></i></button>
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
                  <input type="hidden" name="GateINID"       id="sq_GateINID"       value="<?= $gatein->GateINID; ?>">
                  <input type="hidden" name="form_mode"      id="sq_form_mode"      value="<?= !empty($stack_qc) ? 'edit' : 'add'; ?>">
                  <input type="hidden" name="update_id"      id="sq_update_id"      value="<?= !empty($stack_qc) ? (is_array($stack_qc) ? $stack_qc['id'] : $stack_qc->id) : ''; ?>">
                  <input type="hidden" name="qc_values_json" id="sq_qc_values_json" value="">

                  <table class="mbot5" id="stack_qc_table">
                    <thead>
                      <tr>
                        <th>#</th><th>Item</th><th>Chamber</th><th>Stack</th><th>Lot</th>
                        <th>Weight(MT)</th><th>Bag Qty</th><th>QC Values</th><th>Action</th>
                      </tr>
                    </thead>
                    <tbody id="stack_qc_tbody">
                      <?php
                      $itemDropdownOptions = '<option value="" disabled selected>-- Select Item --</option>';
                      foreach ($itemList as $itm) {
                          $itemDropdownOptions .= '<option value="' . htmlspecialchars($itm['ItemID']) . '" data-name="' . htmlspecialchars($itm['item_name']) . '">' . htmlspecialchars($itm['item_name']) . '</option>';
                      }

                      if (!empty($sqRows)) :
                        foreach ($sqRows as $ri => $sqRow) :
                          $sqRow = is_object($sqRow) ? $sqRow : (object)$sqRow;
                          $savedItemId = htmlspecialchars($sqRow->item_id ?? '');
                          $savedQcStr  = htmlspecialchars($sqRow->qc ?? '');
                      ?>
                      <tr data-row="<?= $ri; ?>">
                        <td class="text-center row-num"><?= $ri + 1; ?></td>
                        <td>
                          <select name="item_id[]" class="form-control item-select-dropdown sq-item-select" style="min-width:130px;">
                            <option value="" disabled>-- Select Item --</option>
                            <?php foreach ($itemList as $itm) : ?>
                              <option value="<?= htmlspecialchars($itm['ItemID']); ?>"
                                data-name="<?= htmlspecialchars($itm['item_name']); ?>"
                                <?= ($savedItemId == $itm['ItemID']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($itm['item_name']); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                        </td>
                        <td><input type="text" name="chamber[]" class="form-control" value="<?= htmlspecialchars($sqRow->chamber ?? ''); ?>"></td>
                        <td><input type="text" name="stack[]"   class="form-control" value="<?= htmlspecialchars($sqRow->stack ?? ''); ?>"></td>
                        <td><input type="text" name="lot[]"     class="form-control" value="<?= htmlspecialchars($sqRow->lot ?? ''); ?>"></td>
                        <td><input type="text" name="weight[]"  class="form-control" value="<?= htmlspecialchars($sqRow->weight ?? ''); ?>"></td>
                        <td><input type="text" name="bag_qty[]" class="form-control" value="<?= htmlspecialchars($sqRow->bag_qty ?? ''); ?>"></td>
                        <td style="white-space:nowrap;">
                          <input type="text" class="form-control qc-display-input" name="qc[]" value="<?= $savedQcStr; ?>" data-saved-qc="<?= $savedQcStr; ?>">
                          <div class="qc-cell-inner">
                            <div class="qc-badge-wrapper" id="qc_badges_<?= $ri; ?>">
                              <?php if (!empty($savedQcStr)) :
                                preg_match_all('/\[([^\]:]+):\s*([^\]]*)\]/', $savedQcStr, $badgeMatches, PREG_SET_ORDER);
                                foreach ($badgeMatches as $bm) : ?>
                                  <span class="qc-badge"><?= htmlspecialchars(trim($bm[1])); ?>: <b><?= htmlspecialchars(trim($bm[2])); ?></b></span>
                                <?php endforeach;
                              else : ?>
                                <span class="qc-badge-empty">-- No QC --</span>
                              <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-info btn-xs sq-item-info-btn" title="View / Fill QC Details" style="margin-left:3px; align-self:flex-start;"><i class="fa fa-info-circle"></i></button>
                          </div>
                        </td>
                        <td style="width:60px; text-align:center;">
                          <button type="button" class="btn btn-success btn-xs sq-add-row" title="Add Row"><i class="fa fa-plus"></i></button>
                          <button type="button" class="btn btn-danger btn-xs sq-remove-row" title="Remove Row"><i class="fa fa-minus"></i></button>
                        </td>
                      </tr>
                      <?php
                        endforeach;
                      else :
                      ?>
                      <tr data-row="0">
                        <td class="text-center row-num">1</td>
                        <td>
                          <select name="item_id[]" class="form-control item-select-dropdown sq-item-select" style="min-width:130px;">
                            <?= $itemDropdownOptions; ?>
                          </select>
                        </td>
                        <td><input type="text" name="chamber[]" class="form-control"></td>
                        <td><input type="text" name="stack[]"   class="form-control"></td>
                        <td><input type="text" name="lot[]"     class="form-control"></td>
                        <td><input type="text" name="weight[]"  class="form-control"></td>
                        <td><input type="text" name="bag_qty[]" class="form-control"></td>
                        <td style="white-space:nowrap;">
                          <input type="text" class="form-control qc-display-input" name="qc[]" value="" data-saved-qc="">
                          <div class="qc-cell-inner">
                            <div class="qc-badge-wrapper" id="qc_badges_0">
                              <span class="qc-badge-empty">-- No QC --</span>
                            </div>
                            <button type="button" class="btn btn-info btn-xs sq-item-info-btn" title="View / Fill QC Details" style="margin-left:3px; align-self:flex-start;"><i class="fa fa-info-circle"></i></button>
                          </div>
                        </td>
                        <td style="width:60px; text-align:center;">
                          <button type="button" class="btn btn-success btn-xs sq-add-row" title="Add Row"><i class="fa fa-plus"></i></button>
                          <button type="button" class="btn btn-danger btn-xs sq-remove-row" title="Remove Row"><i class="fa fa-minus"></i></button>
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
                  <input type="hidden" name="GateINID"  id="tw_GateINID"  value="<?= $gatein->GateINID; ?>">
                  <input type="hidden" name="form_mode" id="tw_form_mode" value="<?= $tw ? 'edit' : 'add'; ?>">
                  <table>
                    <tbody>
                      <tr>
                        <th>Tare Weight(MT)</th><th>Top Image</th><th>Front Image</th><th>Side Image</th>
                        <th>Uploaded By</th><th>Uploaded Date Time</th><th>Action</th>
                      </tr>
                      <tr>
                        <td>
                          <?php if ($tw && isset($tw->value->tare_weight)) : ?>
                            <span id="tw_weight_display"><?= $tw->value->tare_weight; ?></span>
                            <input type="text" name="tare_weight" id="tare_weight" class="form-control" value="<?= $tw->value->tare_weight; ?>" style="display:none;" required>
                          <?php else : ?>
                            <span id="tw_weight_display" style="display:none;"></span>
                            <input type="text" name="tare_weight" id="tare_weight" class="form-control" required>
                          <?php endif; ?>
                        </td>
                        <td id="tw_top_image_cell">
                          <?php if ($tw && !empty($tw->value->TopImage)) : ?>
                            <a href="<?= base_url($tw->value->TopImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_top_image" value="<?= $tw->value->TopImage; ?>">
                          <?php else : ?>
                            <input type="file" name="top_image" id="tw_top_image" accept="image/*" style="display:none;">
                            <label for="tw_top_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="tw_front_image_cell">
                          <?php if ($tw && !empty($tw->value->FrontImage)) : ?>
                            <a href="<?= base_url($tw->value->FrontImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_front_image" value="<?= $tw->value->FrontImage; ?>">
                          <?php else : ?>
                            <input type="file" name="front_image" id="tw_front_image" accept="image/*" style="display:none;">
                            <label for="tw_front_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="tw_side_image_cell">
                          <?php if ($tw && !empty($tw->value->SideImage)) : ?>
                            <a href="<?= base_url($tw->value->SideImage); ?>" target="_blank">View</a>
                            <input type="hidden" name="existing_side_image" value="<?= $tw->value->SideImage; ?>">
                          <?php else : ?>
                            <input type="file" name="side_image" id="tw_side_image" accept="image/*" style="display:none;">
                            <label for="tw_side_image" class="btn btn-xs btn-default mb0 upload-label">Upload</label>
                          <?php endif; ?>
                        </td>
                        <td id="tw_uploaded_by"><?= $tw->UserID ?? '-'; ?></td>
                        <td>
                          <?php if ($tw && !empty($tw->TransDate)) : ?>
                            <span id="UploadedDateTimeCell" data-saved="1"><?= date('Y-m-d H:i:s', strtotime($tw->TransDate)); ?></span>
                          <?php else : ?>
                            <span id="UploadedDateTimeCell"></span>
                          <?php endif; ?>
                        </td>
                        <td style="width:80px;">
                          <?php if ($tw) : ?>
                            <button type="button" class="btn btn-warning btn-xs" id="tw_edit_btn" title="Edit"><i class="fa fa-pencil"></i></button>
                            <button type="submit" class="btn btn-success btn-xs" id="tw_save_btn" style="display:none;" title="Save"><i class="fa fa-save"></i></button>
                          <?php else : ?>
                            <button type="button" class="btn btn-warning btn-xs" id="tw_edit_btn" title="Edit" style="display:none;"><i class="fa fa-pencil"></i></button>
                            <button type="submit" class="btn btn-success btn-xs" id="tw_save_btn" title="Save"><i class="fa fa-save"></i></button>
                          <?php endif; ?>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </form>
              </div>

              <!-- ============================== DEDUCTION MATRIX (PAGE LEVEL) ============================== -->
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Deduction Matrix:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-6 mbot5">
                <table class="mbot5">
                  <thead>
                    <tr><th>Parameter</th><th>QC Value</th><th>Deduction</th><th>Amount</th></tr>
                  </thead>
                  <tbody id="deduction_matrix_body">

                    <!-- ASN Weight -->
                    <tr>
                      <td><b>ASN Weight(MT)</b></td>
                      <td>-</td><td>-</td>
                      <td class="text-right">-</td>
                    </tr>

                    <!-- Purchase Amount -->
                    <tr>
                      <td><b>Purchase Amount</b></td>
                      <td>-</td><td>-</td>
                      <td class="text-right"><?= $inward['NetAmt'] ?? '-'; ?></td>
                    </tr>

                    <!-- Actual Weight -->
                    <tr>
                      <td><b>Actual Weight (MT)</b></td>
                      <td colspan="2">-</td>
                      <td class="text-right">
                        <span id="actual_weight_display" class="actual-weight-value">
                          <?php
                            if ($phpActualWeight !== null) echo number_format($phpActualWeight, 4, '.', '');
                            else echo '-';
                          ?>
                        </span>
                      </td>
                    </tr>

                    <!-- Actual Inward Weight -->
                    <tr>
                      <td><b>Actual Inward Weight (MT)</b></td>
                      <td>-</td><td>-</td>
                      <td class="text-right">
                        <?php echo !empty($savedActualWeight) ? htmlspecialchars($savedActualWeight) : '-'; ?>
                      </td>
                    </tr>

                    <!-- ====== SAVED DEDUCTION ROWS FROM GATEIN (one per row, below Actual Inward Weight) ====== -->
                    <?php if (!empty($savedDeductions)) : ?>
                      <?php foreach ($savedDeductions as $ded) : ?>
                        <tr class="dm-saved-deduction-row">
                          <td><?= htmlspecialchars($ded['name'] ?? '-'); ?></td>
                          <td>-</td>
                          <td>-</td>
                          <td class="text-right deduction-param-val">
                            <?= isset($ded['amount']) ? htmlspecialchars($ded['amount']) : '-'; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else : ?>
                      <!-- Dynamic QC Param rows (JS will inject here when no saved data) -->
                      <tr id="dm_placeholder_row">
                        <td colspan="4" class="text-center" style="color:#aaa; font-style:italic; padding:4px !important;">
                          Select item &amp; enter QC values to see deductions...
                        </td>
                      </tr>
                    <?php endif; ?>

                    <!-- JS injects .dm-qc-row rows here when calculating live -->

                    <!-- Bag Weight -->
                    <tr id="dm_bag_weight_row">
                      <td><b>Bag Weight</b></td>
                      <td>-</td><td>-</td>
                      <td class="text-right" id="dm_bag_weight_val">
                        <?= !empty($savedBagWeight) ? htmlspecialchars($savedBagWeight) : '23'; ?>
                      </td>
                    </tr>

                    <!-- Total Deduction -->
                    <tr>
                      <td><b>Total Deduction</b></td>
                      <td>-</td><td>-</td>
                      <td class="text-right" id="dm_total_deduction_val">
                        <?= !empty($savedTotalDeduction) ? htmlspecialchars($savedTotalDeduction) : '-'; ?>
                      </td>
                    </tr>

                    <!-- ====== SAVED FINAL RATE ROWS FROM GATEIN (one per item) ====== -->
                    <?php if (!empty($savedFinalRates)) : ?>
                      <?php foreach ($savedFinalRates as $fr) : ?>
                        <tr class="dm-saved-final-rate-row">
                          <td>
                            <b>Final Rate/MT</b>
                            <small style="background:#e8f4fd; border-radius:3px; padding:1px 5px; color:#31708f;">
                              <?= htmlspecialchars($fr['name'] ?? ''); ?>
                            </small>
                          </td>
                          <td>-</td><td>-</td>
                          <td class="text-right">
                            <b><?= isset($fr['value']) ? htmlspecialchars($fr['value']) : '-'; ?></b>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- JS Final Rate rows anchor -->
                    <tr id="dm_final_rate_section"><td colspan="4" style="padding:0 !important; border:none !important;"></td></tr>

                    <!-- Net Amount -->
                    <tr class="<?= !empty($savedNetAmt) ? 'dm-saved-net-amount-row' : ''; ?>">
                      <td><b>Net Amount</b></td>
                      <td>-</td><td>-</td>
                      <td class="text-right" id="dm_net_amount_val">
                        <?= !empty($savedNetAmt) ? htmlspecialchars($savedNetAmt) : '-'; ?>
                      </td>
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
                      <button type="button" class="btn btn-success btn-sm" id="gateout_pass_btn" style="margin-left: 236px; background-color: #1986f3;"
                        <?= !empty($gate_out) ? 'disabled' : ''; ?>>
                        <?php if (!empty($gate_out)) : ?>
                          <i class="fa fa-check"></i> Gate Out Done
                        <?php else : ?>
                          <i class="fa fa-sign-out"></i> GATE OUT PASS
                        <?php endif; ?>
                      </button>
                    </div>
                    <hr class="hr_style" />
                  </div>
                  <div class="col-md-12 mbot5">
                    <table>
                      <tbody>
                        <tr><th>Gate Out By</th><th>Gate Out Date Time</th></tr>
                        <tr>
                          <td id="gateout_by_cell"><?= !empty($gate_out) ? $gate_out->UserID : '-'; ?></td>
                          <td id="gateout_datetime_cell"><?= !empty($gate_out) && !empty($gate_out->value->Time) ? $gate_out->value->Time : '-'; ?></td>
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
                      <button type="button" class="btn btn-success btn-sm" id="exit_marked_btn" style="margin-left: 285px; background-color: #1986f3;"
                        <?= !empty($gate_exit) ? 'disabled' : ''; ?>>
                        <?php if (!empty($gate_exit)) : ?>
                          <i class="fa fa-check"></i> Exit Done
                        <?php else : ?>
                          <i class="fa fa-sign-out"></i> EXIT MARKED
                        <?php endif; ?>
                      </button>
                    </div>
                    <hr class="hr_style" />
                  </div>
                  <div class="col-md-12 mbot5">
                    <table>
                      <tbody>
                        <tr><th>Exit By</th><th>Exit Date Time</th></tr>
                        <tr>
                          <td id="exit_by_cell"><?= !empty($gate_exit) ? $gate_exit->UserID : '-'; ?></td>
                          <td id="exit_datetime_cell"><?= !empty($gate_exit) && !empty($gate_exit->value->Time) ? $gate_exit->value->Time : '-'; ?></td>
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
  #imageModal .modal-dialog { max-height: 60vh; margin-top: 10vh; margin-bottom: 10vh; }
  #imageModal .modal-content { max-height: 60vh; overflow: auto; }
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

<!-- ======================== ITEM DETAIL POPUP MODAL (WITH QC CALC + DEDUCTION MATRIX) ======================== -->
<div class="modal fade" id="itemDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 860px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-info-circle"></i> &nbsp;<span id="itemModalTitle">Item Details</span>
          <small id="itemModalBasicRate" style="margin-left:12px; font-size:11px; background:#fff; color:#50607b; border-radius:3px; padding:1px 8px;"></small>
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">

        <!-- Saved QC display -->
        <div id="savedQcDisplay" style="display:none; margin-bottom:8px; padding:6px 10px; background:#dff0d8; border:1px solid #5cb85c; border-radius:4px; font-size:11px; color:#3c763d;">
          <b><i class="fa fa-check-circle"></i> Saved QC:</b> <span id="savedQcText"></span>
        </div>

        <!-- ====== QC PARAMETERS TABLE ====== -->
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
              <tr><td colspan="9" class="text-center" style="padding:8px !important;">Select an item to load QC parameters...</td></tr>
            </tbody>
          </table>
        </div>
        <div id="itemQCLoader" style="display:none; text-align:center; padding:10px;">
          <i class="fa fa-spinner fa-spin fa-2x" style="color:#50607b;"></i>
          <span style="margin-left:8px; font-size:13px;">Loading QC Parameters...</span>
        </div>

        <!-- ====== DEDUCTION MATRIX (per parameter) — shown after QC value typed ====== -->
        <div id="deductionMatrixSection" style="display:none;">
          <h6><i class="fa fa-table"></i> Deduction Matrix — <span id="dmParamName"></span></h6>
          <div id="dmTableWrapper"></div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success btn-sm" id="itemModalSaveQCBtn"><i class="fa fa-save"></i> Save QC Values</button>
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

// ======================== ITEM LIST FROM PHP ========================
var itemDataList = <?= json_encode($itemList); ?>;
var itemDataMap  = {};
itemDataList.forEach(function(item) { itemDataMap[item.ItemID] = item; });

// ======================== QC VALUES STORE ========================
// Structure: qcValuesStore[rowIndex][paramId] = { pct, paramName, deduction, calcBy, reductionAmt, basicRate }
var qcValuesStore = <?= json_encode($qcValuesStorePhp); ?>;

// Full QC API data per stack row: modalQCData[rowIndex] = [{id, ParameterName, MinValue, ...deduction_matrix}, ...]
var modalQCData = {};

// Currently open modal row index and item
var currentModalRowIndex = null;
var currentModalItemID   = null;

// Saved gatein deduction data from PHP
var savedGateinDeductions = <?= json_encode($savedDeductions); ?>;
var savedGateinFinalRates = <?= json_encode($savedFinalRates); ?>;
var savedGateinNetAmt     = '<?= addslashes($savedNetAmt); ?>';
var savedGateinTotalDed   = '<?= addslashes($savedTotalDeduction); ?>';

// ======================== HTML ESCAPE ========================
function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ======================== PARSE QC STRING ========================
function parseQCString(qcStr) {
    if (!qcStr) return [];
    var results = [];
    var regex = /\[([^\]:]+):\s*([^\]]*)\]/g, match;
    while ((match = regex.exec(qcStr)) !== null) {
        results.push({ name: match[1].trim(), value: match[2].trim() });
    }
    return results;
}

// ======================== BUILD QC DISPLAY STRING ========================
function buildQCDisplayString(rowIndex) {
    var stored = qcValuesStore[rowIndex];
    if (!stored || Object.keys(stored).length === 0) return '';
    var parts = [];
    $.each(stored, function(paramId, vals) {
        if (vals.pct !== '' && vals.pct !== undefined) {
            var label = vals.paramName ? vals.paramName : ('Param#' + paramId);
            parts.push(label + ': ' + vals.pct);
        }
    });
    return parts.length === 0 ? '' : '[' + parts.join('], [') + ']';
}

// ======================== RENDER QC BADGES (badges only, no page matrix) ========================
function renderQCBadges(rowIndex) {
    var $row       = $('#stack_qc_tbody tr[data-row="' + rowIndex + '"]');
    var $qcInput   = $row.find('.qc-display-input');
    var $badgeWrap = $row.find('.qc-badge-wrapper');

    var displayStr = buildQCDisplayString(rowIndex);
    if (!displayStr) displayStr = $qcInput.data('saved-qc') || '';
    if (displayStr) $qcInput.val(displayStr);

    $badgeWrap.empty();
    var parsed = parseQCString(displayStr);
    if (parsed.length > 0) {
        parsed.forEach(function(itm) {
            $badgeWrap.append('<span class="qc-badge">' + escHtml(itm.name) + ': <b>' + escHtml(itm.value) + '</b></span>');
        });
    } else {
        $badgeWrap.append('<span class="qc-badge-empty">-- No QC --</span>');
    }
    // NOTE: No page matrix rebuild here — page load must not disturb saved PHP rows
}

// ======================== ACTUAL WEIGHT CALCULATION ========================
function calculateActualWeight() {
    var grossVal = parseFloat($('#gw_weight_display').text());
    if (isNaN(grossVal) || grossVal === 0) grossVal = parseFloat($('#gross_weight').val()) || 0;
    var tareVal = parseFloat($('#tw_weight_display').text());
    if (isNaN(tareVal) || tareVal === 0) tareVal = parseFloat($('#tare_weight').val()) || 0;
    if (grossVal > 0 && tareVal > 0) {
        $('#actual_weight_display').text((grossVal - tareVal).toFixed(4)).css('color', '#3c763d');
    } else {
        $('#actual_weight_display').text('-').css('color', '');
    }
}

$(document).on('input keyup', '#gross_weight', function () {
    calculateActualWeight();
    // Re-validate tare weight if already entered
    var tw = parseFloat($('#tare_weight').val()) || 0;
    if (tw > 0) {
        var gw = parseFloat($(this).val()) || 0;
        if (tw >= gw && gw > 0) {
            $('#tare_weight').css('border-color', '#e74c3c');
        } else {
            $('#tare_weight').css('border-color', '');
        }
    }
});

$(document).on('input keyup', '#tare_weight', function () {
    calculateActualWeight();
    var grossVal = parseFloat($('#gw_weight_display').text());
    if (isNaN(grossVal) || grossVal === 0) grossVal = parseFloat($('#gross_weight').val()) || 0;
    var tareVal  = parseFloat($(this).val()) || 0;
    if (grossVal > 0 && tareVal > 0) {
        if (tareVal >= grossVal) {
            $(this).css('border-color', '#e74c3c');
            $('#tare_weight_error').remove();
            $(this).after('<span id="tare_weight_error" style="color:#e74c3c; font-size:10px; display:block;">Tare Weight must be less than Gross Weight (' + grossVal + ' MT)</span>');
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
    var val  = $(this).val();
    if (!/[0-9.]/.test(char)) { e.preventDefault(); return; }
    if (char === '.' && val.indexOf('.') !== -1) { e.preventDefault(); return; }
});
$(document).on('keydown', '.qc-Percentage_Wise-input', function (e) {
    var allowedKeys = [8, 9, 13, 27, 46, 37, 38, 39, 40, 110, 190];
    if (allowedKeys.indexOf(e.which) !== -1) return;
    if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].indexOf(e.which) !== -1) return;
});
$(document).on('paste', '.qc-Percentage_Wise-input', function (e) {
    var $input = $(this);
    setTimeout(function () {
        var cleaned = $input.val().replace(/[^0-9.]/g, '');
        var parts = cleaned.split('.');
        if (parts.length > 2) cleaned = parts[0] + '.' + parts.slice(1).join('');
        $input.val(cleaned).trigger('input');
    }, 0);
});

// ======================== PAGE LOAD ========================
$(document).ready(function () {
    // Render badges only — saved PHP rows remain visible as-is
    $('#stack_qc_tbody tr').each(function () {
        renderQCBadges(parseInt($(this).data('row')));
    });
    calculateActualWeight();
});

// ======================== FIND DEDUCTION (3-tier matching) ========================
function findDeduction(qcValue, deductionMatrix) {
    if (!deductionMatrix || deductionMatrix.length === 0) return null;
    var qv = parseFloat(qcValue);
    if (isNaN(qv)) return null;
    // Tier 1: Exact match
    for (var i = 0; i < deductionMatrix.length; i++) {
        if (parseFloat(deductionMatrix[i].Value) === qv) return parseFloat(deductionMatrix[i].Deduction);
    }
    // Tier 2: Closest lower or equal (floor match)
    var bestVal = null, bestDed = null;
    for (var i = 0; i < deductionMatrix.length; i++) {
        var dmVal = parseFloat(deductionMatrix[i].Value);
        if (dmVal <= qv && (bestVal === null || dmVal > bestVal)) { bestVal = dmVal; bestDed = parseFloat(deductionMatrix[i].Deduction); }
    }
    if (bestDed !== null) return bestDed;
    // Tier 3: Minimum value in matrix
    var minVal = null, minDed = null;
    for (var i = 0; i < deductionMatrix.length; i++) {
        var dmVal = parseFloat(deductionMatrix[i].Value);
        if (minVal === null || dmVal < minVal) { minVal = dmVal; minDed = parseFloat(deductionMatrix[i].Deduction); }
    }
    return minDed;
}

function getMatchedMatrixValue(qcValue, deductionMatrix) {
    if (!deductionMatrix || deductionMatrix.length === 0) return null;
    var qv = parseFloat(qcValue);
    if (isNaN(qv)) return null;
    for (var i = 0; i < deductionMatrix.length; i++) {
        if (parseFloat(deductionMatrix[i].Value) === qv) return parseFloat(deductionMatrix[i].Value);
    }
    var bestVal = null;
    for (var i = 0; i < deductionMatrix.length; i++) {
        var dmVal = parseFloat(deductionMatrix[i].Value);
        if (dmVal <= qv && (bestVal === null || dmVal > bestVal)) bestVal = dmVal;
    }
    if (bestVal !== null) return bestVal;
    var minVal = null;
    for (var i = 0; i < deductionMatrix.length; i++) {
        var dmVal = parseFloat(deductionMatrix[i].Value);
        if (minVal === null || dmVal < minVal) minVal = dmVal;
    }
    return minVal;
}

function calcReductionAmount(calculationBy, basicRate, deduction) {
    var br  = parseFloat(basicRate) || 0;
    var ded = parseFloat(deduction) || 0;
    if (calculationBy && calculationBy.toLowerCase() === 'percentage') return (br * ded / 100);
    return ded;
}

// ======================== RENDER DEDUCTION MATRIX TABLE (modal mini-table) ========================
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
    deductionMatrix.forEach(function(dm, idx) {
        var isMatch = (highlightVal !== null && parseFloat(dm.Value) === highlightVal);
        html += '<tr class="' + (isMatch ? 'dm-highlight' : '') + '">'
              + '<td class="text-center">' + (idx+1) + '</td>'
              + '<td>' + escHtml(dm.Value) + (isMatch ? ' &#8592;' : '') + '</td>'
              + '<td>' + escHtml(dm.Deduction) + '</td></tr>';
    });
    html += '</tbody></table>';
    $('#dmTableWrapper').html(infoBanner + html);
    $('#dmParamName').text(paramName);
    $('#deductionMatrixSection').show();
}

// ======================== PAGE-LEVEL DEDUCTION MATRIX UPDATE ========================
/*
 * Strategy: Instead of hide/show all rows, we do TARGETED row updates.
 * - Each param has a unique <tr id="dm_param_row_{key}"> in the page table.
 * - On QC change, only update that specific row's Amount cell.
 * - Saved PHP rows (.dm-saved-deduction-row) are NEVER hidden.
 * - If a param already exists as a saved row, update it in-place.
 * - If it's a new param (not in saved), inject a new live row.
 * - Total Deduction and Net Amount are recalculated from all visible rows.
 */

function updatePageDeductionMatrixForParam(paramName, qcValue, deductionMatrix, calculationBy, basicRate) {
    var key       = paramName.toLowerCase().replace(/\s+/g, '_');
    var rowId     = 'dm_param_row_' + key;
    var $existing = $('#' + rowId);

    var deduction    = findDeduction(qcValue, deductionMatrix);
    var reductionAmt = null;
    var dedDisplay   = '-';
    var amtDisplay   = '-';

    if (deduction !== null && basicRate > 0) {
        reductionAmt = calcReductionAmount(calculationBy, basicRate, deduction);
        dedDisplay   = parseFloat(deduction).toFixed(2) + (calculationBy.toLowerCase() === 'percentage' ? ' %' : '');
        amtDisplay   = parseFloat(reductionAmt).toFixed(4);
    }

    var calcByBadge = calculationBy
        ? '<small style="background:#e8f4fd;border-radius:3px;padding:1px 4px;color:#31708f;">' + escHtml(calculationBy) + '</small>'
        : '';

    if ($existing.length) {
        // Row already exists (saved or previously injected) — update Amount cell only
        $existing.find('td').eq(1).text(parseFloat(qcValue).toFixed(4));
        $existing.find('td').eq(2).html('<span class="deduction-param-val">' + dedDisplay + '</span>');
        $existing.find('td').eq(3).html('<b>' + amtDisplay + '</b>');
        $existing.show(); // make sure it's visible
    } else {
        // New param not in saved rows — inject a live row before Bag Weight
        var newRow = '<tr id="' + rowId + '" class="dm-live-row" style="background-color:#fdf8e1 !important;">'
            + '<td>' + escHtml(paramName) + ' ' + calcByBadge + '</td>'
            + '<td class="text-right">' + parseFloat(qcValue).toFixed(4) + '</td>'
            + '<td class="text-right"><span class="deduction-param-val">' + dedDisplay + '</span></td>'
            + '<td class="text-right"><b>' + amtDisplay + '</b></td>'
            + '</tr>';
        $('#dm_bag_weight_row').before(newRow);
    }

    // Recalculate totals from all visible deduction rows
    recalcPageTotals(basicRate, calculationBy);
}

function recalcPageTotals(basicRate, calculationBy) {
    // Sum all visible deduction amounts (saved + live rows)
    var totalDed = 0;
    $('#deduction_matrix_body tr.dm-saved-deduction-row:visible, #deduction_matrix_body tr.dm-live-row:visible').each(function() {
        var amtText = $(this).find('td').eq(3).text().trim();
        var amt = parseFloat(amtText);
        if (!isNaN(amt)) totalDed += amt;
    });

    // Update Final Rate rows per item
    // Collect all live + saved final rate rows and update them
    var itemReductions = {};
    $('#deduction_matrix_body tr.dm-saved-deduction-row:visible, #deduction_matrix_body tr.dm-live-row:visible').each(function() {
        // Skip final rate rows
        if ($(this).hasClass('dm-saved-final-rate-row') || $(this).find('td:first b').text().indexOf('Final Rate') !== -1) return;
        // We cannot easily map row → item here without extra data attributes
        // So we use a simpler approach: just update total and net
    });

    $('#dm_total_deduction_val').text(totalDed > 0 ? totalDed.toFixed(4) : '-');

    // Net amount: basicRate - totalDed (simplified single-item; multi-item handled separately)
    // For multi-item, we update Final Rate rows individually via updateFinalRateForItem
}

function updateFinalRateForItem(itemID, itemName, basicRate, totalReductionForItem) {
    var key     = 'dm_finalrate_' + (itemID || '').replace(/[^a-zA-Z0-9]/g, '_');
    var $existing = $('#' + key);
    var finalRate = basicRate > 0 ? (basicRate - totalReductionForItem) : null;
    var display   = finalRate !== null ? finalRate.toFixed(4) : '-';

    if ($existing.length) {
        $existing.find('td').eq(3).html('<b>' + display + '</b>');
    } else {
        var row = '<tr id="' + key + '" class="dm-live-row dm-saved-final-rate-row" style="background-color:#dff0d8 !important;font-weight:bold;color:#3c763d;">'
            + '<td><b>Final Rate/MT</b> <small style="background:#e8f4fd;border-radius:3px;padding:1px 5px;color:#31708f;">' + escHtml(itemName) + '</small></td>'
            + '<td>-</td><td>-</td>'
            + '<td class="text-right"><b>' + display + '</b></td>'
            + '</tr>';
        $('#dm_final_rate_section').after(row);
    }
}

// ======================== FULL REBUILD (called only after modal save/close) ========================
// Rebuilds entire page matrix from qcValuesStore + modalQCData
// ======================== TARGETED SINGLE PARAM ROW UPDATE ========================
// Updates ONLY the Amount cell of one param row in the page Deduction Matrix.
// Never hides or touches any other row.
// ======================== COMPUTE PARAM SUMS FROM ALL ROWS ========================
// Returns { paramKey: { paramName, sumQcValue, deductionMatrix, calculationBy, basicRate, itemID } }
// Reads from qcValuesStore (all rows) + live modal inputs for currentModalRowIndex
function computeAllParamSums() {
    var sums = {};

    // Step 1: Sum from qcValuesStore (all saved rows except current modal row)
    $.each(qcValuesStore, function(rowIdx, params) {
        var ri      = parseInt(rowIdx);
        var $row    = $('#stack_qc_tbody tr[data-row="' + ri + '"]');
        var itemID  = $row.find('.sq-item-select').val() || '';
        var item    = itemDataMap[itemID] || {};
        var br      = parseFloat(item.BasicRate) || 0;
        var qcRows  = modalQCData[ri] || [];

        $.each(params, function(pid, vals) {
            if (!vals.paramName || vals.pct === '' || vals.pct === undefined || vals.pct === null) return;
            var qv  = parseFloat(vals.pct);
            if (isNaN(qv)) return;
            var key = vals.paramName.toLowerCase().trim();

            // Get deductionMatrix from modalQCData for this row
            var dm = null, cb = vals.calcBy || '';
            qcRows.forEach(function(r) {
                if ((r.ParameterName || '').toLowerCase().trim() === key) {
                    dm = r.deduction_matrix;
                    if (!cb) cb = r.CalculationBy || '';
                }
            });

            if (!sums[key]) {
                sums[key] = { paramName: vals.paramName, sumQcValue: 0, deductionMatrix: dm, calculationBy: cb, basicRate: br, itemID: itemID };
            }
            sums[key].sumQcValue += qv;
            if (!sums[key].deductionMatrix && dm) sums[key].deductionMatrix = dm;
            if (!sums[key].calculationBy && cb) sums[key].calculationBy = cb;
            if (br > 0) sums[key].basicRate = br;
            if (itemID) sums[key].itemID = itemID;
        });
    });

    // Step 2: Overlay live modal inputs (replace stored value for current row's params)
    if (currentModalRowIndex !== null && currentModalItemID !== null) {
        var mItem  = itemDataMap[currentModalItemID] || {};
        var mBR    = parseFloat(mItem.BasicRate) || 0;
        var mRows  = modalQCData[currentModalRowIndex] || [];

        // Remove currentModalRowIndex contribution from sums (will re-add from live inputs)
        $.each(qcValuesStore[currentModalRowIndex] || {}, function(pid, vals) {
            if (!vals.paramName) return;
            var key = vals.paramName.toLowerCase().trim();
            if (sums[key]) {
                sums[key].sumQcValue -= parseFloat(vals.pct) || 0;
            }
        });

        // Add live modal input values
        $('#itemQCTableBody tr').each(function() {
            var pInput   = $(this).find('.qc-Percentage_Wise-input');
            var paramIdx = parseInt($(this).data('param-idx'));
            var pName    = pInput.data('param-name') || '';
            var pVal     = pInput.val().trim();
            if (!pName || pVal === '' || isNaN(parseFloat(pVal))) return;

            var qv  = parseFloat(pVal);
            var key = pName.toLowerCase().trim();
            var qcRowData = mRows[paramIdx] || null;
            var dm  = qcRowData ? qcRowData.deduction_matrix : null;
            var cb  = qcRowData ? (qcRowData.CalculationBy || '') : '';

            if (!sums[key]) {
                sums[key] = { paramName: pName, sumQcValue: 0, deductionMatrix: dm, calculationBy: cb, basicRate: mBR, itemID: currentModalItemID };
            }
            sums[key].sumQcValue += qv;
            if (!sums[key].deductionMatrix && dm) sums[key].deductionMatrix = dm;
            if (!sums[key].calculationBy && cb) sums[key].calculationBy = cb;
            if (mBR > 0) sums[key].basicRate = mBR;
        });
    }

    return sums;
}

// ======================== UPDATE ALL PARAM ROWS IN PAGE DEDUCTION MATRIX ========================
// Called whenever any QC value changes (live typing or after save).
// Updates Amount, Deduction, and QC Value columns for every param.
// Never hides or removes any row.
function updateAllParamRows() {
    var sums = computeAllParamSums();

    // Per-item reduction totals for Final Rate
    var itemReductions = {}; // itemID -> { itemName, basicRate, total }

    $.each(sums, function(key, s) {
        if (!s.deductionMatrix || s.sumQcValue <= 0) return;

        var deduction    = findDeduction(s.sumQcValue, s.deductionMatrix);
        var reductionAmt = (deduction !== null && s.basicRate > 0)
                           ? calcReductionAmount(s.calculationBy, s.basicRate, deduction) : null;

        var dedDisplay = '-';
        var amtDisplay = '-';
        if (deduction !== null) {
            dedDisplay = parseFloat(deduction).toFixed(2) + (s.calculationBy.toLowerCase() === 'percentage' ? ' %' : '');
        }
        if (reductionAmt !== null) amtDisplay = parseFloat(reductionAmt).toFixed(4);

        var calcByBadge = s.calculationBy
            ? '<small style="background:#e8f4fd;border-radius:3px;padding:1px 4px;color:#31708f;">' + escHtml(s.calculationBy) + '</small>'
            : '';

        // Find matching saved PHP row by param name text (eq0 = name, eq1 = QC val, eq2 = deduction, eq3 = amount)
        var matched = false;
        $('#deduction_matrix_body tr.dm-saved-deduction-row').each(function() {
            var cellText = $(this).find('td').eq(0).text().trim().toLowerCase();
            if (cellText === key) {
                $(this).find('td').eq(1).text(parseFloat(s.sumQcValue).toFixed(4));
                $(this).find('td').eq(2).text(dedDisplay);
                $(this).find('td').eq(3).html('<b>' + amtDisplay + '</b>');
                matched = true;
                return false;
            }
        });

        if (!matched) {
            // Inject or update live row
            var liveRowId = 'dm_live_' + key.replace(/\s+/g, '_');
            var $lr = $('#' + liveRowId);
            if ($lr.length) {
                $lr.find('td').eq(1).text(parseFloat(s.sumQcValue).toFixed(4));
                $lr.find('td').eq(2).text(dedDisplay);
                $lr.find('td').eq(3).html('<b>' + amtDisplay + '</b>');
            } else {
                var newRow = '<tr id="' + liveRowId + '" class="dm-live-row" style="background-color:#fdf8e1 !important;">'
                    + '<td>' + escHtml(s.paramName) + ' ' + calcByBadge + '</td>'
                    + '<td class="text-right">' + parseFloat(s.sumQcValue).toFixed(4) + '</td>'
                    + '<td class="text-right">' + dedDisplay + '</td>'
                    + '<td class="text-right"><b>' + amtDisplay + '</b></td>'
                    + '</tr>';
                $('#dm_bag_weight_row').before(newRow);
            }
        }

        // Accumulate per-item reduction
        var iid = s.itemID;
        var iObj = itemDataMap[iid] || {};
        if (!itemReductions[iid]) {
            itemReductions[iid] = { itemName: iObj.item_name || iid, basicRate: s.basicRate, total: 0 };
        }
        if (reductionAmt !== null) itemReductions[iid].total += reductionAmt;
    });

    // Update Total Deduction
    var totalDed = 0;
    $.each(itemReductions, function(iid, d) { totalDed += d.total; });
    $('#dm_total_deduction_val').text(totalDed > 0 ? totalDed.toFixed(4) : '-');

    // Update Final Rate and Net Amount per item
    var netAmt = 0;
    $.each(itemReductions, function(iid, d) {
        var fr      = d.basicRate > 0 ? (d.basicRate - d.total) : null;
        var display = fr !== null ? fr.toFixed(4) : '-';
        if (fr !== null) netAmt += fr;

        var matched = false;
        $('#deduction_matrix_body tr.dm-saved-final-rate-row').each(function() {
            var sName = $(this).find('small').text().trim().toLowerCase();
            if (sName === d.itemName.toLowerCase()) {
                $(this).find('td').eq(3).html('<b>' + display + '</b>');
                matched = true;
                return false;
            }
        });
        if (!matched) {
            var frId = 'dm_fr_' + iid.replace(/[^a-zA-Z0-9]/g, '_');
            var $fr  = $('#' + frId);
            if ($fr.length) {
                $fr.find('td').eq(3).html('<b>' + display + '</b>');
            } else {
                var row = '<tr id="' + frId + '" class="dm-live-row dm-live-final-rate-row" style="background-color:#dff0d8 !important;font-weight:bold;color:#3c763d;">'
                    + '<td><b>Final Rate/MT</b> <small style="background:#e8f4fd;border-radius:3px;padding:1px 5px;color:#31708f;">' + escHtml(d.itemName) + '</small></td>'
                    + '<td>-</td><td>-</td>'
                    + '<td class="text-right"><b>' + display + '</b></td>'
                    + '</tr>';
                $('#dm_final_rate_section').after(row);
            }
        }
    });

    $('#dm_net_amount_val').text(netAmt > 0 ? netAmt.toFixed(4) : '-');
}

// Legacy wrappers — now both just call updateAllParamRows
function updateSingleParamRow(paramName, qcValue, deductionMatrix, calculationBy, basicRate, precomputedSum) {
    updateAllParamRows();
}
function recalcTotalsOnly() {
    updateAllParamRows();
}
function updateFinalRateRowForItem(itemID, itemName, basicRate) {
    updateAllParamRows();
}

// clearSingleParamRow — when a QC value is cleared, re-run full param update
// (other rows may still have values for this param)
function clearSingleParamRow(paramName) {
    updateAllParamRows();
}

function rebuildFullPageMatrix() {
    // Remove only injected live rows (not PHP-saved rows)
    $('#deduction_matrix_body .dm-live-row').remove();

    // Restore saved PHP rows
    $('.dm-saved-deduction-row').show();
    $('.dm-saved-final-rate-row').show();

    // Restore saved totals as baseline
    $('#dm_total_deduction_val').text(savedGateinTotalDed || '-');
    $('#dm_net_amount_val').text(savedGateinNetAmt || '-');

    if (Object.keys(qcValuesStore).length === 0) return;

    // Group QC values by param across all stack rows
    var grouped  = {};
    var keyOrder = [];
    var itemReductions = {}; // itemID → { itemName, basicRate, totalReduction }

    $.each(qcValuesStore, function(rowIdx, params) {
        var rowIndex  = parseInt(rowIdx);
        var $row      = $('#stack_qc_tbody tr[data-row="' + rowIndex + '"]');
        var itemID    = $row.find('.sq-item-select').val() || '';
        var item      = itemDataMap[itemID] || null;
        var basicRate = item ? (parseFloat(item.BasicRate) || 0) : 0;
        var qcRows    = modalQCData[rowIndex] || [];

        $.each(params, function(paramId, vals) {
            if (!vals.pct || vals.pct === '') return;
            var qcValue   = parseFloat(vals.pct);
            if (isNaN(qcValue)) return;
            var paramName = vals.paramName || '';
            if (!paramName) return;
            var key = paramName.toLowerCase().trim();

            var qcRowData = null;
            var calcBy    = vals.calcBy || '';
            qcRows.forEach(function(r) {
                if ((r.ParameterName || '').toLowerCase().trim() === key) { qcRowData = r; if (!calcBy) calcBy = r.CalculationBy || ''; }
            });

            if (!grouped[key]) {
                grouped[key] = { paramName: paramName, sumQcValue: 0, itemID: itemID, basicRate: basicRate, qcRowData: qcRowData, calcBy: calcBy };
                keyOrder.push(key);
            }
            grouped[key].sumQcValue += qcValue;
            if (!grouped[key].qcRowData && qcRowData) { grouped[key].qcRowData = qcRowData; grouped[key].calcBy = calcBy; }
            if (basicRate > 0) grouped[key].basicRate = basicRate;
        });
    });

    if (keyOrder.length === 0) return;

    // Hide matching saved rows and inject updated live rows
    var totalDed = 0;
    var $bagWeightRow = $('#dm_bag_weight_row');

    keyOrder.forEach(function(key) {
        var g         = grouped[key];
        var sumVal    = g.sumQcValue;
        var basicRate = g.basicRate;
        var calcBy    = g.calcBy;
        var qcRowData = g.qcRowData;

        var deduction = qcRowData ? findDeduction(sumVal, qcRowData.deduction_matrix) : null;
        var reductionAmt = (deduction !== null && basicRate > 0) ? calcReductionAmount(calcBy, basicRate, deduction) : null;
        if (reductionAmt !== null) totalDed += reductionAmt;

        var dedDisplay = '-', amtDisplay = '-';
        if (deduction !== null) {
            dedDisplay = parseFloat(deduction).toFixed(2) + (calcBy.toLowerCase() === 'percentage' ? ' %' : '');
        }
        if (reductionAmt !== null) amtDisplay = parseFloat(reductionAmt).toFixed(4);

        var calcByBadge = calcBy
            ? '<small style="background:#e8f4fd;border-radius:3px;padding:1px 4px;color:#31708f;">' + escHtml(calcBy) + '</small>'
            : '';

        // Find saved row with matching param name and update in-place
        var matchedSaved = false;
        $('.dm-saved-deduction-row').each(function() {
            var rowParamName = $(this).find('td').eq(0).clone().find('small').remove().end().text().trim().toLowerCase();
            if (rowParamName === g.paramName.toLowerCase()) {
                $(this).find('td').eq(1).text(parseFloat(sumVal.toFixed(4)));
                $(this).find('td').eq(2).html('<span class="deduction-param-val">' + dedDisplay + '</span>');
                $(this).find('td').eq(3).html('<b>' + amtDisplay + '</b>');
                matchedSaved = true;
            }
        });

        if (!matchedSaved) {
            // Inject new live row
            var newRow = '<tr class="dm-live-row" style="background-color:#fdf8e1 !important;">'
                + '<td>' + escHtml(g.paramName) + ' ' + calcByBadge + '</td>'
                + '<td class="text-right">' + parseFloat(sumVal.toFixed(4)) + '</td>'
                + '<td class="text-right"><span class="deduction-param-val">' + dedDisplay + '</span></td>'
                + '<td class="text-right"><b>' + amtDisplay + '</b></td>'
                + '</tr>';
            $bagWeightRow.before(newRow);
        }

        // Accumulate per-item reductions
        var itemID = g.itemID;
        if (!itemReductions[itemID]) {
            var itemObj = itemDataMap[itemID] || {};
            itemReductions[itemID] = { itemName: itemObj.item_name || itemID, basicRate: basicRate, totalReduction: 0 };
        }
        if (reductionAmt !== null) itemReductions[itemID].totalReduction += reductionAmt;
    });

    $('#dm_total_deduction_val').text(totalDed > 0 ? totalDed.toFixed(4) : '-');

    // Update Final Rate rows
    var netAmount = 0;
    $.each(itemReductions, function(itemID, data) {
        var finalRate = data.basicRate > 0 ? (data.basicRate - data.totalReduction) : null;
        if (finalRate !== null) netAmount += finalRate;
        var display = finalRate !== null ? finalRate.toFixed(4) : '-';

        // Find saved final rate row for this item
        var matched = false;
        $('.dm-saved-final-rate-row').each(function() {
            var sName = $(this).find('small').text().trim().toLowerCase();
            if (sName === data.itemName.toLowerCase()) {
                $(this).find('td').eq(3).html('<b>' + display + '</b>');
                matched = true;
            }
        });

        if (!matched) {
            var row = '<tr class="dm-live-row" style="background-color:#dff0d8 !important;font-weight:bold;color:#3c763d;">'
                + '<td><b>Final Rate/MT</b> <small style="background:#e8f4fd;border-radius:3px;padding:1px 5px;color:#31708f;">' + escHtml(data.itemName) + '</small></td>'
                + '<td>-</td><td>-</td>'
                + '<td class="text-right"><b>' + display + '</b></td>'
                + '</tr>';
            $('#dm_final_rate_section').after(row);
        }
    });

    $('#dm_net_amount_val').text(netAmount > 0 ? netAmount.toFixed(4) : '-');
}

// ======================== RECALC MODAL ROW ========================
function recalcModalRow($tr, qcRows, itemID) {
    var paramIdx  = parseInt($tr.data('param-idx'));
    var qcRowData = qcRows[paramIdx];
    if (!qcRowData) return;

    var $qcInput  = $tr.find('.qc-Percentage_Wise-input');
    var qcVal     = $qcInput.val().trim();
    var item      = itemDataMap[itemID] || {};
    var basicRate = parseFloat(item.BasicRate) || 0;
    var calcBy    = qcRowData.CalculationBy || '';
    var paramName = qcRowData.ParameterName || '';
    var paramKey  = paramName.toLowerCase().trim();

    var $matchedCell = $tr.find('.qc-matched-deduction');
    var $finalCell   = $tr.find('.qc-final-amount');

    if (qcVal === '' || isNaN(parseFloat(qcVal))) {
        $matchedCell.html('<span style="color:#aaa;">-</span>');
        $finalCell.html('<span style="color:#aaa;">-</span>');
        renderDeductionMatrixTable(paramName, qcRowData.deduction_matrix, null);
        clearSingleParamRow(paramName);
        recalcTotalsOnly();
        return;
    }

    // ── Calculate SUM: this row's value + other stack rows' saved values for same param ──
    var currentVal = parseFloat(qcVal) || 0;
    var otherSum   = 0;
    $.each(qcValuesStore, function(rowIdx, params) {
        if (parseInt(rowIdx) === currentModalRowIndex) return; // skip current row
        $.each(params, function(pid, vals) {
            if ((vals.paramName || '').toLowerCase().trim() === paramKey && vals.pct !== '' && vals.pct !== undefined) {
                otherSum += parseFloat(vals.pct) || 0;
            }
        });
    });
    var totalSum = currentVal + otherSum;

    // ── Modal columns: show sum-based deduction ──
    var deductionOnSum = findDeduction(totalSum, qcRowData.deduction_matrix);
    if (deductionOnSum === null) {
        $matchedCell.html('<span style="color:#aaa;">-</span>');
        $finalCell.html('<span style="color:#aaa;">-</span>');
        renderDeductionMatrixTable(paramName, qcRowData.deduction_matrix, totalSum);
        updateSingleParamRow(paramName, currentVal, qcRowData.deduction_matrix, calcBy, basicRate, totalSum);
        recalcTotalsOnly();
        return;
    }

    var reductionAmt = calcReductionAmount(calcBy, basicRate, deductionOnSum);
    var dedDisplay   = parseFloat(deductionOnSum).toFixed(2) + (calcBy.toLowerCase() === 'percentage' ? '%' : '');

    // Show sum info in modal if there are values from other rows
    var sumInfo = '';
    if (otherSum > 0) {
        sumInfo = ' <small style="color:#31708f; background:#d9edf7; border-radius:3px; padding:1px 4px;">'
                + 'This: ' + currentVal + ' + Others: ' + otherSum.toFixed(4) + ' = Sum: ' + totalSum.toFixed(4)
                + '</small>';
    }

    var matchedVal  = getMatchedMatrixValue(totalSum, qcRowData.deduction_matrix);
    var isExact     = (matchedVal !== null && parseFloat(matchedVal) === parseFloat(totalSum));
    var matchSuffix = isExact ? '' : ' <small style="color:#8a6d3b;">(~' + matchedVal + ')</small>';

    $matchedCell.html('<span class="qc-calc-badge"><i class="fa fa-arrow-down"></i> ' + escHtml(dedDisplay) + '</span>' + matchSuffix + sumInfo);
    $finalCell.html('<b>' + reductionAmt.toFixed(4) + '</b>');

    // Show deduction matrix highlighted on totalSum
    renderDeductionMatrixTable(paramName, qcRowData.deduction_matrix, totalSum);

    // Update page Deduction Matrix row with precomputed totalSum (no double count)
    updateSingleParamRow(paramName, currentVal, qcRowData.deduction_matrix, calcBy, basicRate, totalSum);
    recalcTotalsOnly();
}

// ======================== SAVE CURRENT MODAL QC VALUES ========================
function saveCurrentQCValues() {
    if (currentModalRowIndex === null) return;
    if (!qcValuesStore[currentModalRowIndex]) qcValuesStore[currentModalRowIndex] = {};

    var qcRows    = modalQCData[currentModalRowIndex] || [];
    var item      = itemDataMap[currentModalItemID] || {};
    var basicRate = parseFloat(item.BasicRate) || 0;

    // Save each param value to store AND update its page row in-place
    $('#itemQCTableBody tr').each(function() {
        var $tr       = $(this);
        var paramIdx  = parseInt($tr.data('param-idx'));
        var qcRowData = qcRows[paramIdx];
        if (!qcRowData) return;
        var pctInput  = $tr.find('.qc-Percentage_Wise-input');
        if (!pctInput.length) return;
        var nameAttr  = pctInput.attr('name') || '';
        var paramName = pctInput.data('param-name') || '';
        var match     = nameAttr.match(/\[(\w+)\]/);
        if (!match || !match[1]) return;
        var paramId      = match[1];
        var qcVal        = pctInput.val();
        var deduction    = findDeduction(qcVal, qcRowData.deduction_matrix);
        var calcBy       = qcRowData.CalculationBy || '';
        var reductionAmt = (deduction !== null && basicRate > 0) ? calcReductionAmount(calcBy, basicRate, deduction) : null;

        // Update store
        qcValuesStore[currentModalRowIndex][paramId] = {
            pct: qcVal, paramName: paramName, deduction: deduction,
            calcBy: calcBy, reductionAmt: reductionAmt, basicRate: basicRate
        };

        // Update ONLY this param's row in page Deduction Matrix (no hide/show of other rows)
        if (paramName && qcVal !== '') {
            // Compute sum: other rows + current value
            var pKey    = paramName.toLowerCase().trim();
            var saveSum = parseFloat(qcVal) || 0;
            $.each(qcValuesStore, function(rIdx, rParams) {
                if (parseInt(rIdx) === currentModalRowIndex) return;
                $.each(rParams, function(pid, vals) {
                    if ((vals.paramName || '').toLowerCase().trim() === pKey && vals.pct !== '' && vals.pct !== undefined) {
                        saveSum += parseFloat(vals.pct) || 0;
                    }
                });
            });
            updateSingleParamRow(paramName, qcVal, qcRowData.deduction_matrix, calcBy, basicRate, saveSum);
        }
    });

    renderQCBadges(currentModalRowIndex);
    // Recalc totals only (no row hide/show)
    recalcTotalsOnly();
}

// ======================== RESTORE QC VALUES AFTER AJAX ========================
function restoreQCValues(rowIndex, qcRows) {
    var savedVals = qcValuesStore[rowIndex] || {};
    var nameToPct = {};
    $.each(savedVals, function(key, vals) {
        if (vals.paramName) nameToPct[vals.paramName.toLowerCase().trim()] = vals.pct || '';
    });
    if (Object.keys(nameToPct).length === 0) return;

    $('#itemQCTableBody tr').each(function() {
        var $tr       = $(this);
        var pctInput  = $tr.find('.qc-Percentage_Wise-input');
        var paramName = pctInput.data('param-name') || '';
        if (pctInput.length && paramName) {
            var lookupKey = paramName.toLowerCase().trim();
            if (nameToPct.hasOwnProperty(lookupKey)) {
                pctInput.val(nameToPct[lookupKey]);
                recalcModalRow($tr, qcRows, currentModalItemID);
            }
        }
    });

    // Rebuild store with fresh paramIds
    var freshStore = {};
    $('#itemQCTableBody tr').each(function() {
        var $tr       = $(this);
        var pctInput  = $tr.find('.qc-Percentage_Wise-input');
        if (!pctInput.length) return;
        var nameAttr  = pctInput.attr('name') || '';
        var paramName = pctInput.data('param-name') || '';
        var match     = nameAttr.match(/\[(\w+)\]/);
        if (match && match[1]) {
            var paramIdx     = parseInt($tr.data('param-idx'));
            var qcRowData    = (qcRows && qcRows[paramIdx]) ? qcRows[paramIdx] : null;
            var qcVal        = pctInput.val();
            var deduction    = qcRowData ? findDeduction(qcVal, qcRowData.deduction_matrix) : null;
            var calcBy       = qcRowData ? (qcRowData.CalculationBy || '') : '';
            var item         = itemDataMap[currentModalItemID] || {};
            var basicRate    = parseFloat(item.BasicRate) || 0;
            var reductionAmt = (deduction !== null && basicRate > 0) ? calcReductionAmount(calcBy, basicRate, deduction) : null;
            freshStore[match[1]] = { pct: qcVal, paramName: paramName, deduction: deduction, calcBy: calcBy, reductionAmt: reductionAmt, basicRate: basicRate };
        }
    });
    qcValuesStore[rowIndex] = freshStore;
}

// ======================== MODAL CLOSE — AUTO SAVE ========================
$('#itemDetailModal').on('hide.bs.modal', function () {
    saveCurrentQCValues();
    currentModalRowIndex = null;
    currentModalItemID   = null;
    $('#savedQcDisplay').hide();
    $('#savedQcText').text('');
    $('#deductionMatrixSection').hide();
});

// ======================== MODAL SAVE QC BUTTON ========================
$(document).on('click', '#itemModalSaveQCBtn', function () {
    saveCurrentQCValues();
    toastr.success('QC values saved! They will be submitted with Stack Details.');
    $('#itemDetailModal').modal('hide');
});

// ======================== LIVE QC INPUT -> RECALCULATE ========================
$(document).on('input change', '#itemQCTableBody .qc-Percentage_Wise-input', function () {
    var $tr    = $(this).closest('tr');
    var qcRows = modalQCData[currentModalRowIndex] || [];
    recalcModalRow($tr, qcRows, currentModalItemID);
});

// ======================== OPEN ITEM POPUP ========================
function openItemDetailPopup(rowIndex, selectedItemID) {
    saveCurrentQCValues();
    if (!selectedItemID) { toastr.warning('Please select an item first!'); return; }
    var item = itemDataMap[selectedItemID];
    if (!item) { toastr.warning('Item details not found!'); return; }

    currentModalRowIndex = rowIndex;
    currentModalItemID   = selectedItemID;

    $('#itemModalTitle').text(item.item_name || selectedItemID);
    $('#itemModalBasicRate').text('Basic Rate: \u20B9' + (parseFloat(item.BasicRate) || 0).toFixed(2) + ' / MT');

    var $row    = $('#stack_qc_tbody tr[data-row="' + rowIndex + '"]');
    var savedQc = $row.find('.qc-display-input').data('saved-qc') || $row.find('.qc-display-input').val() || '';
    if (savedQc && savedQc !== '-- No QC --') {
        $('#savedQcText').text(savedQc);
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
                rows.forEach(function(row, idx) {
                    qcHtml += '<tr data-param-idx="' + idx + '">'
                        + '<td class="text-center">' + (idx+1) + '</td>'
                        + '<td>' + escHtml(row.ParameterName || '-') + '</td>'
                        + '<td>' + escHtml(row.MinValue || '-') + '</td>'
                        + '<td>' + escHtml(row.MaxValue || '-') + '</td>'
                        + '<td>' + escHtml(row.BaseValue || '-') + '</td>'
                        + '<td><span style="font-size:10px;background:#e8f4fd;border-radius:3px;padding:1px 4px;">' + escHtml(row.CalculationBy || '-') + '</span></td>'
                        + '<td><input type="text" step="any" class="form-control qc-Percentage_Wise-input qc-value-input"'
                        + ' inputmode="decimal"'
                        + ' name="qc_Percentage_Wise[' + escHtml(row.id) + ']"'
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

// ======================== ITEM SELECT -> POPUP ========================
$(document).on('change', '.sq-item-select', function () {
    openItemDetailPopup($(this).closest('tr').data('row'), $(this).val());
});
$(document).on('click', '.sq-item-info-btn', function () {
    var $row = $(this).closest('tr');
    openItemDetailPopup($row.data('row'), $row.find('.sq-item-select').val());
});

// ======================== ADD ROW DROPDOWN HTML ========================
function buildItemDropdownHtml() {
    var html = '<select name="item_id[]" class="form-control item-select-dropdown sq-item-select" style="min-width:130px;">'
        + '<option value="" disabled selected>-- Select Item --</option>';
    itemDataList.forEach(function(itm) {
        html += '<option value="' + escHtml(itm.ItemID) + '" data-name="' + escHtml(itm.item_name) + '">' + escHtml(itm.item_name) + '</option>';
    });
    html += '</select>';
    return html;
}

// ======================== LIVE DATE TIME ========================
function updateCurrentDateTime() {
    var now = new Date();
    var formatted = now.getFullYear() + '-'
        + ('0'+(now.getMonth()+1)).slice(-2) + '-'
        + ('0'+now.getDate()).slice(-2) + ' '
        + ('0'+now.getHours()).slice(-2) + ':'
        + ('0'+now.getMinutes()).slice(-2) + ':'
        + ('0'+now.getSeconds()).slice(-2);
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
        var fileName  = this.files[0].name;
        var shortName = fileName.length > 10 ? fileName.substring(0,10) + '...' : fileName;
        $('label[for="' + this.id + '"]').removeClass('btn-default').addClass('btn-success uploaded').html('<i class="fa fa-check"></i> ' + shortName);
    }
});

// ======================== GROSS WEIGHT EDIT ========================
$(document).on('click', '#gw_edit_btn', function () {
    $('#gw_weight_display').hide();
    $('#gross_weight').show().focus();
    switchToUpload('gw_top_image_cell',   'top_image',   'gw_top_image',   'existing_top_image');
    switchToUpload('gw_front_image_cell', 'front_image', 'gw_front_image', 'existing_front_image');
    switchToUpload('gw_side_image_cell',  'side_image',  'gw_side_image',  'existing_side_image');
    $(this).hide(); $('#gw_save_btn').show();
});

// ======================== TARE WEIGHT EDIT ========================
$(document).on('click', '#tw_edit_btn', function () {
    $('#tw_weight_display').hide();
    $('#tare_weight').show().focus();
    switchToUpload('tw_top_image_cell',   'top_image',   'tw_top_image',   'existing_top_image');
    switchToUpload('tw_front_image_cell', 'front_image', 'tw_front_image', 'existing_front_image');
    switchToUpload('tw_side_image_cell',  'side_image',  'tw_side_image',  'existing_side_image');
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

// ======================== STACK QC — ADD ROW ========================
$(document).on('click', '.sq-add-row', function () {
    var tbody    = $('#stack_qc_tbody');
    var rowCount = tbody.find('tr').length;
    var newIdx   = rowCount;
    var newRow   = '<tr data-row="' + newIdx + '">'
        + '<td class="text-center row-num">' + (rowCount+1) + '</td>'
        + '<td>' + buildItemDropdownHtml() + '</td>'
        + '<td><input type="text" name="chamber[]" class="form-control"></td>'
        + '<td><input type="text" name="stack[]"   class="form-control"></td>'
        + '<td><input type="text" name="lot[]"     class="form-control"></td>'
        + '<td><input type="text" name="weight[]"  class="form-control"></td>'
        + '<td><input type="text" name="bag_qty[]" class="form-control"></td>'
        + '<td style="white-space:nowrap;">'
            + '<input type="text" class="form-control qc-display-input" name="qc[]" value="" data-saved-qc="">'
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
    $('#stack_qc_tbody tr').each(function(i) {
        var oldIdx = parseInt($(this).data('row'));
        $(this).find('.row-num').text(i+1);
        $(this).attr('data-row', i);
        $(this).find('.qc-badge-wrapper').attr('id', 'qc_badges_' + i);
        if (qcValuesStore[oldIdx] !== undefined) newStore[i] = qcValuesStore[oldIdx];
        if (modalQCData[oldIdx]   !== undefined) newModal[i] = modalQCData[oldIdx];
    });
    qcValuesStore = newStore;
    modalQCData   = newModal;
    $('#stack_qc_tbody tr').each(function() { renderQCBadges(parseInt($(this).data('row'))); });
}

// ======================== STACK QC — SUBMIT ========================
$('#stack_qc_form').on('submit', function (e) {
    e.preventDefault();
    saveCurrentQCValues();
    var item_id=[], chamber=[], stack=[], lot=[], weight=[], bag_qty=[], qc=[];
    $('#stack_qc_tbody tr').each(function() {
        item_id.push($(this).find('select[name="item_id[]"]').val());
        chamber.push($(this).find('input[name="chamber[]"]').val());
        stack.push($(this).find('input[name="stack[]"]').val());
        lot.push($(this).find('input[name="lot[]"]').val());
        weight.push($(this).find('input[name="weight[]"]').val());
        bag_qty.push($(this).find('input[name="bag_qty[]"]').val());
        qc.push($(this).find('input[name="qc[]"]').val());
    });
    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    var postData = {
        GateINID: $('#sq_GateINID').val(), form_mode: $('#sq_form_mode').val(), update_id: $('#sq_update_id').val(),
        'item_id[]': item_id, 'chamber[]': chamber, 'stack[]': stack,
        'lot[]': lot, 'weight[]': weight, 'bag_qty[]': bag_qty, 'qc[]': qc,
        qc_values_json: JSON.stringify(qcValuesStore)
    };
    postData['<?= $this->security->get_csrf_token_name(); ?>'] = csrfToken;
    $('#sq_qc_values_json').val(JSON.stringify(qcValuesStore));
    $('#sq_update_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    $.ajax({
        url: '<?= admin_url('purchase/Inwards/SaveStackQCDetails'); ?>',
        type: 'POST', data: postData, dataType: 'json',
        success: function(res) {
            $('#sq_update_btn').prop('disabled', false).html('<i class="fa fa-save"></i> UPDATE STACK DETAILS');
            if (res.success == true) {
                toastr.success(res.message || 'Stack QC details saved successfully!');
                if (res.data && res.data.id) { $('#sq_update_id').val(res.data.id); $('#sq_form_mode').val('edit'); }
                $('#stack_qc_tbody tr').each(function() { var $qi=$(this).find('.qc-display-input'); $qi.attr('data-saved-qc', $qi.val()); });
            } else { toastr.error(res.message || 'Failed to save Stack QC details!'); }
        },
        error: function() { $('#sq_update_btn').prop('disabled',false).html('<i class="fa fa-save"></i> UPDATE STACK DETAILS'); toastr.error('Something went wrong!'); }
    });
});

// ======================== GROSS WEIGHT SUBMIT ========================
$('#gross_weight_form').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());
    $.ajax({
        url: '<?= admin_url('purchase/Inwards/SaveGrossWeight'); ?>',
        type: 'POST', data: formData, dataType: 'json', processData: false, contentType: false,
        success: function(res) {
            if (res.success == true) {
                toastr.success(res.message || 'Gross Weight saved successfully!');
                $('#gw_loaded_by').html(res.data.UserID || '');
                if (res.data.value) {
                    if (res.data.value.TopImage)   $('#gw_top_image_cell').html('<a href="'+getAbsUrl(res.data.value.TopImage)+'" target="_blank">View</a><input type="hidden" name="existing_top_image" value="'+res.data.value.TopImage+'">');
                    if (res.data.value.FrontImage) $('#gw_front_image_cell').html('<a href="'+getAbsUrl(res.data.value.FrontImage)+'" target="_blank">View</a><input type="hidden" name="existing_front_image" value="'+res.data.value.FrontImage+'">');
                    if (res.data.value.SideImage)  $('#gw_side_image_cell').html('<a href="'+getAbsUrl(res.data.value.SideImage)+'" target="_blank">View</a><input type="hidden" name="existing_side_image" value="'+res.data.value.SideImage+'">');
                }
                $('#gw_update_id').val(res.data.id); $('#gw_form_mode').val('edit');
                var gwVal = (res.data.value && res.data.value.gross_weight) ? res.data.value.gross_weight : $('#gross_weight').val();
                $('#gross_weight').val(gwVal).hide(); $('#gw_weight_display').text(gwVal).show();
                if (res.data.TransDate) $('#loadedDateTimeCell').text(res.data.TransDate).attr('data-saved','1');
                $('#gw_save_btn').hide();
                if ($('#gw_edit_btn').length===0) $('#gw_save_btn').after('<button type="button" class="btn btn-warning btn-xs" id="gw_edit_btn" title="Edit"><i class="fa fa-pencil"></i></button>');
                else $('#gw_edit_btn').show();
                calculateActualWeight();
            } else { toastr.error(res.message || 'Failed to save Gross Weight!'); }
        },
        error: function() { toastr.error('Something went wrong!'); }
    });
});

// ======================== TARE WEIGHT SUBMIT ========================
$('#tare_weight_form').on('submit', function(e) {
    e.preventDefault();

    // Validation: Tare Weight must be less than Gross Weight
    var grossVal = parseFloat($('#gw_weight_display').text());
    if (isNaN(grossVal) || grossVal === 0) grossVal = parseFloat($('#gross_weight').val()) || 0;
    var tareVal  = parseFloat($('#tare_weight').val()) || 0;

    if (grossVal === 0) {
        toastr.warning('Please enter Gross Weight first before saving Tare Weight.');
        return;
    }
    if (tareVal <= 0) {
        toastr.warning('Please enter a valid Tare Weight.');
        return;
    }
    if (tareVal >= grossVal) {
        toastr.error('Tare Weight (' + tareVal + ' MT) cannot be greater than or equal to Gross Weight (' + grossVal + ' MT).');
        $('#tare_weight').focus().css('border-color', '#e74c3c');
        return;
    }

    // Clear error border if valid
    $('#tare_weight').css('border-color', '');

    var formData = new FormData(this);
    formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());
    $.ajax({
        url: '<?= admin_url('purchase/Inwards/SaveTareWeight'); ?>',
        type: 'POST', data: formData, dataType: 'json', processData: false, contentType: false,
        success: function(res) {
            if (res.success == true) {
                toastr.success(res.message || 'Tare Weight saved successfully!');
                $('#tw_uploaded_by').html(res.data.UserID || '');
                if (res.data.value) {
                    if (res.data.value.TopImage)   $('#tw_top_image_cell').html('<a href="'+getAbsUrl(res.data.value.TopImage)+'" target="_blank">View</a><input type="hidden" name="existing_top_image" value="'+res.data.value.TopImage+'">');
                    if (res.data.value.FrontImage) $('#tw_front_image_cell').html('<a href="'+getAbsUrl(res.data.value.FrontImage)+'" target="_blank">View</a><input type="hidden" name="existing_front_image" value="'+res.data.value.FrontImage+'">');
                    if (res.data.value.SideImage)  $('#tw_side_image_cell').html('<a href="'+getAbsUrl(res.data.value.SideImage)+'" target="_blank">View</a><input type="hidden" name="existing_side_image" value="'+res.data.value.SideImage+'">');
                }
                $('#tw_update_id').val(res.data.id); $('#tw_form_mode').val('edit');
                var twVal = (res.data.value && res.data.value.tare_weight) ? res.data.value.tare_weight : $('#tare_weight').val();
                $('#tare_weight').val(twVal).hide(); $('#tw_weight_display').text(twVal).show();
                if (res.data.TransDate) $('#UploadedDateTimeCell').text(res.data.TransDate).attr('data-saved','1');
                $('#tw_save_btn').hide();
                if ($('#tw_edit_btn').length===0) $('#tw_save_btn').after('<button type="button" class="btn btn-warning btn-xs" id="tw_edit_btn" title="Edit"><i class="fa fa-pencil"></i></button>');
                else $('#tw_edit_btn').show();
                calculateActualWeight();
            } else { toastr.error(res.message || 'Failed to save Tare Weight!'); }
        },
        error: function() { toastr.error('Something went wrong!'); }
    });
});

// ======================== CONVEYOR SUBMIT ========================
$('#conveyor_form').on('submit', function(e) {
    e.preventDefault();
    var conveyorVal = $('#conveyor_id').val();
    if (!conveyorVal) { toastr.warning('Please select a conveyor!'); return; }
    var formData = new FormData(this);
    formData.append('conveyor_id', conveyorVal);
    formData.append('<?= $this->security->get_csrf_token_name(); ?>', $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val());
    $.ajax({
        url: '<?= admin_url('purchase/Inwards/SaveConveyorAssignment'); ?>',
        type: 'POST', data: formData, dataType: 'json', processData: false, contentType: false,
        success: function(res) {
            if (res.success == true) {
                toastr.success(res.message || 'Conveyor saved successfully!');
                $('#cv_added_by').html(res.data.UserID || '');
                $('#cv_update_id').val(res.data.id); $('#cv_form_mode').val('edit');
                var cvVal = (res.data.value && res.data.value.ConveyorID) ? res.data.value.ConveyorID : conveyorVal;
                $('#conveyor_id').val(cvVal).prop('disabled', true);
                if (res.data.TransDate) $('#cv_datetime_cell').text(res.data.TransDate).attr('data-saved','1');
                $('#cv_save_btn').hide();
                if ($('#cv_edit_btn').length===0) $('#cv_save_btn').after('<button type="button" class="btn btn-warning btn-xs" id="cv_edit_btn" title="Edit"><i class="fa fa-pencil"></i></button>');
                else $('#cv_edit_btn').show();
            } else { toastr.error(res.message || 'Failed to save conveyor!'); }
        },
        error: function() { toastr.error('Something went wrong!'); }
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
        success: function(res) {
            if (res.success == true) {
                toastr.success(res.message || 'Gate Out Pass saved successfully!');
                $('#gateout_by_cell').text(res.data.UserID || '-');
                $('#gateout_datetime_cell').text((res.data.value && res.data.value.Time) ? res.data.value.Time : (res.data.TransDate || '-'));
                $btn.prop('disabled', true).html('<i class="fa fa-check"></i> Gate Out Done');
            } else { toastr.error(res.message || 'Failed to save Gate Out Pass!'); $btn.prop('disabled',false).html('<i class="fa fa-sign-out"></i> GATE OUT PASS'); }
        },
        error: function() { toastr.error('Something went wrong!'); $btn.prop('disabled',false).html('<i class="fa fa-sign-out"></i> GATE OUT PASS'); }
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
        success: function(res) {
            if (res.success == true) {
                toastr.success(res.message || 'Exit marked successfully!');
                $('#exit_by_cell').text(res.data.UserID || '-');
                $('#exit_datetime_cell').text((res.data.value && res.data.value.Time) ? res.data.value.Time : (res.data.TransDate || '-'));
                $btn.prop('disabled', true).html('<i class="fa fa-check"></i> Exit Done');
            } else { toastr.error(res.message || 'Failed to mark Exit!'); $btn.prop('disabled',false).html('<i class="fa fa-sign-out"></i> EXIT MARKED'); }
        },
        error: function() { toastr.error('Something went wrong!'); $btn.prop('disabled',false).html('<i class="fa fa-sign-out"></i> EXIT MARKED'); }
    });
});

// ======================== ADVANCE PAYMENT — SAVE DEDUCTION MATRIX ========================
$(document).on('click', '#advance_payment_btn', function () {
    var GateINID  = $('#sq_GateINID').val() || '<?= $gatein->GateINID; ?>';
    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

    var actualWeight   = $('#actual_weight_display').text().trim();
    if (actualWeight   === '-' || actualWeight === '')   actualWeight   = '';
    var bagWeight      = $('#dm_bag_weight_val').text().trim();
    if (bagWeight      === '-' || bagWeight === '')      bagWeight      = '';
    var totalDeduction = $('#dm_total_deduction_val').text().trim();
    if (totalDeduction === '-' || totalDeduction === '') totalDeduction = '';
    var netAmt         = $('#dm_net_amount_val').text().trim();
    if (netAmt         === '-' || netAmt === '')         netAmt         = '';

    var deductionArr = [];
    $('#deduction_matrix_body tr.dm-saved-deduction-row:visible, #deduction_matrix_body tr.dm-live-row:visible').each(function() {
        if ($(this).find('td:first b').text().indexOf('Final Rate') !== -1) return;
        var $tds    = $(this).find('td');
        var rawName = $tds.eq(0).clone().find('small').remove().end().text().trim();
        var amount  = $tds.eq(3).text().trim();
        if (rawName && amount && amount !== '-') deductionArr.push({ name: rawName, amount: amount });
    });

    var finalRateArr = [];
    $('#deduction_matrix_body tr.dm-saved-final-rate-row:visible, #deduction_matrix_body tr.dm-live-row.dm-saved-final-rate-row:visible').each(function() {
        var $tds     = $(this).find('td');
        var itemName = $tds.eq(0).find('small').text().trim();
        var value    = $tds.eq(3).text().trim();
        if (itemName && value && value !== '-') finalRateArr.push({ name: itemName, value: value });
    });

    var qcMatrix = [];
    $.each(qcValuesStore, function(rowIdx, params) {
        var $row   = $('#stack_qc_tbody tr[data-row="' + rowIdx + '"]');
        var itemID = $row.find('.sq-item-select').val() || '';
        var item   = itemDataMap[itemID] || {};
        $.each(params, function(paramId, vals) {
            if (vals.pct !== '' && vals.pct !== undefined) {
                qcMatrix.push({ item_id: itemID, item_name: item.item_name || '', param_name: vals.paramName || '',
                    qc_value: vals.pct, deduction: vals.deduction !== null ? vals.deduction : '',
                    calc_by: vals.calcBy || '',
                    reduction: vals.reductionAmt !== null && vals.reductionAmt !== undefined ? parseFloat(vals.reductionAmt).toFixed(4) : ''
                });
            }
        });
    });

    if (!GateINID) { toastr.warning('GateINID not found!'); return; }
    if (deductionArr.length === 0 && finalRateArr.length === 0) { toastr.warning('No deduction data found. Please fill QC values first.'); return; }

    var $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    var postData = {
        GateINID: GateINID, ActualWeight: actualWeight, TotalDeduction: totalDeduction,
        NetAmt: netAmt, BagWeight: bagWeight,
        Deduction: JSON.stringify(deductionArr), FinalRate: JSON.stringify(finalRateArr), QCMatrix: JSON.stringify(qcMatrix)
    };
    postData['<?= $this->security->get_csrf_token_name(); ?>'] = csrfToken;
    $.ajax({
        url: '<?= admin_url('purchase/Inwards/SaveDeductionMatrix'); ?>',
        type: 'POST', data: postData, dataType: 'json',
        success: function(res) {
            $btn.prop('disabled', false).html('<i class="fa fa-money"></i> ADVANCE PAYMENT');
            if (res.success == true) {
                toastr.success(res.message || 'Deduction Matrix saved successfully!');
                savedGateinDeductions = deductionArr;
                savedGateinFinalRates = finalRateArr;
                savedGateinNetAmt     = netAmt;
                savedGateinTotalDed   = totalDeduction;
            } else { toastr.error(res.message || 'Failed to save Deduction Matrix!'); }
        },
        error: function() { $btn.prop('disabled',false).html('<i class="fa fa-money"></i> ADVANCE PAYMENT'); toastr.error('Something went wrong! Please try again.'); }
    });
});

</script>