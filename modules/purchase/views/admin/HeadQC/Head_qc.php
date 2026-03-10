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

  /* ====== DEDUCTION MATRIX TABLE (RIGHT SIDE) ====== */
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

  .sq-stack-select,
  .sq-lot-select {
    min-width: 120px;
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

  /* ====== DEDUCTION COUNTER INPUT ====== */
  .dm-counter-input {
    width: 75px !important;
    min-width: 65px !important;
    text-align: center;
    border: 1px solid #5bc0de !important;
    border-radius: 3px;
    background: #f0faff;
    font-size: 11px;
    padding: 1px 4px !important;
    color: #31708f;
    font-weight: bold;
  }
  .dm-counter-input:focus {
    outline: none;
    border-color: #50607b !important;
    background: #fff;
  }

  /* ====== HEAD QC INPUT ====== */
  .dm-head-qc-input {
    width: 80px !important;
    min-width: 70px !important;
    text-align: center;
    border: 1px solid #f0ad4e !important;
    border-radius: 3px;
    background: #fffdf0;
    font-size: 11px;
    padding: 1px 4px !important;
    color: #8a6d3b;
    font-weight: bold;
  }
  .dm-head-qc-input:focus {
    outline: none;
    border-color: #e67e22 !important;
    background: #fff;
  }

  /* Amount cell updated by Head QC */
  .dm-amount-cell {
    text-align: right;
    min-width: 80px;
  }
  .dm-amount-updated {
    background-color: #fff3cd !important;
  }
</style>

<?php
// ====== CHAMBER LIST ======
$chamberList = !empty($chamber) && is_array($chamber) ? $chamber : [];

$itemList = [];
if (!empty($inward['history']) && is_array($inward['history'])) {
  foreach ($inward['history'] as $histRow) {
    $itemList[] = [
      'ItemID'      => $histRow['ItemID'] ?? '',
      'item_name'   => $histRow['item_name'] ?? '',
      'BasicRate'   => $histRow['BasicRate'] ?? '',
      'SaleRate'    => $histRow['SaleRate'] ?? '',
      'UnitWeight'  => $histRow['UnitWeight'] ?? '',
      'WeightUnit'  => $histRow['WeightUnit'] ?? '',
      'OrderQty'    => $histRow['OrderQty'] ?? '',
      'SuppliedIn'  => $histRow['SuppliedIn'] ?? '',
      'OrderAmt'    => $histRow['OrderAmt'] ?? '',
      'NetOrderAmt' => $histRow['NetOrderAmt'] ?? '',
      'igst'        => $histRow['igst'] ?? '',
      'igstamt'     => $histRow['igstamt'] ?? '',
      'cgst'        => $histRow['cgst'] ?? '',
      'sgst'        => $histRow['sgst'] ?? '',
      'batch_no'    => $histRow['batch_no'] ?? '',
      'expiry_date' => $histRow['expiry_date'] ?? '',
    ];
  }
}

// ====== sqRows: load from $stack_qc_details ======
$sqRows = !empty($stack_qc_details) && is_array($stack_qc_details) ? $stack_qc_details : [];

// ====== qcValuesStore (PHP): keyed by parameter_id ======
$qcValuesStorePhp = [];
foreach ($sqRows as $ri => $sqRow) {
  $sqRow  = is_array($sqRow) ? $sqRow : (array) $sqRow;
  $qcArr  = isset($sqRow['qc']) && is_array($sqRow['qc']) ? $sqRow['qc'] : [];
  foreach ($qcArr as $qcEntry) {
    $qcEntry    = is_array($qcEntry) ? $qcEntry : (array) $qcEntry;
    $paramId    = isset($qcEntry['parameter_id']) ? (string) $qcEntry['parameter_id'] : '';
    $paramVal   = isset($qcEntry['value'])         ? (string) $qcEntry['value']         : '';
    $dedAmt     = isset($qcEntry['deductionamt'])  ? (float)  $qcEntry['deductionamt']  : 0;
    $qcDetailId = isset($qcEntry['id'])            ? (string) $qcEntry['id']            : '';
    $hvalue     = isset($qcEntry['hvalue'])        ? (string) $qcEntry['hvalue']        : '';
    if ($paramId === '') continue;
    $qcValuesStorePhp[$ri][$paramId] = [
      'pct'          => $paramVal,
      'paramName'    => '',
      'parameter_id' => $paramId,
      'qcDetailId'   => $qcDetailId,
      'hvalue'       => $hvalue,
      'deduction'    => null,
      'calcBy'       => '',
      'reductionAmt' => $dedAmt,
      'basicRate'    => 0,
    ];
  }
}

// ====== Gross / Tare weight for summary display ======
$gw = !empty($gross_weight) ? (object) $gross_weight : null;
if ($gw && isset($gw->value) && is_array($gw->value)) $gw->value = (object) $gw->value;

$tw = !empty($tare_weight) ? (object) $tare_weight : null;
if ($tw && isset($tw->value) && is_array($tw->value)) $tw->value = (object) $tw->value;

$phpGrossWeight  = isset($gw->value->gross_weight) ? (float) $gw->value->gross_weight : 0;
$phpTareWeight   = isset($tw->value->tare_weight)  ? (float) $tw->value->tare_weight  : 0;
$phpActualWeight = ($phpGrossWeight > 0 && $phpTareWeight > 0) ? ($phpGrossWeight - $phpTareWeight) : null;

// ====== HELPER: Build Chamber Dropdown HTML (PHP) ======
function buildChamberDropdownPhp($chamberList, $selectedId = '')
{
  $html  = '<select name="chamber[]" class="form-control">';
  $html .= '<option value="" disabled ' . (empty($selectedId) ? 'selected' : '') . '>-- Select Chamber --</option>';
  foreach ($chamberList as $ch) {
    $isSelected = (!empty($selectedId) && $selectedId == $ch['id']) ? 'selected' : '';
    $html .= '<option value="' . htmlspecialchars($ch['id']) . '" ' . $isSelected . '>'
      . htmlspecialchars($ch['ChamberName']) . '</option>';
  }
  $html .= '</select>';
  return $html;
}

// ====== Determine head QC form mode: 'add' or 'edit' based on existing hvalue ======
$headQcFormMode = 'add';
foreach ($sqRows as $sqRow) {
  $sqRow = is_array($sqRow) ? $sqRow : (array) $sqRow;
  $qcArr = isset($sqRow['qc']) && is_array($sqRow['qc']) ? $sqRow['qc'] : [];
  foreach ($qcArr as $qcEntry) {
    $qcEntry = is_array($qcEntry) ? $qcEntry : (array) $qcEntry;
    if (!empty($qcEntry['hvalue']) && $qcEntry['hvalue'] !== '') {
      $headQcFormMode = 'edit';
      break 2;
    }
  }
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
                style="background-color:#fff !important; margin-bottom:0px !important;">
                <li class="breadcrumb-item"><a href="<?= admin_url(); ?>"><b><i class="fa fa-home fa-fw fa-lg"></i></b></a></li>
                <li class="breadcrumb-item active text-capitalize"><b>Purchase</b></li>
                <li class="breadcrumb-item active" aria-current="page"><b>Head QC</b></li>
              </ol>
            </nav>
            <hr class="hr_style" />
            <br />

            <div class="row">

              <!-- ===== CENTER QC & STACK DETAILS ===== -->
              <div style="display:none">
                <div class="col-md-12 mbot5">
                  <h4 class="bold p_style">Center QC &amp; Stack Details:</h4>
                  <hr class="hr_style" />
                </div>
                <div class="col-md-12 mbot5">
                  <form id="stack_qc_form">
                    <input type="hidden" name="GateINID"   id="sq_GateINID"   value="<?= $gatein->GateINID; ?>">
                    <input type="hidden" name="form_mode"  id="sq_form_mode"  value="<?= !empty($sqRows) ? 'edit' : 'add'; ?>">
                    <input type="hidden" name="update_id"  id="sq_update_id"
                      value="<?= !empty($stack_qc) ? (is_array($stack_qc) ? ($stack_qc['id'] ?? '') : ($stack_qc->id ?? '')) : ''; ?>">
                    <input type="hidden" name="sq_gatein_id" id="sq_gatein_id" value="<?= $inward['gatein_id'] ?? ''; ?>">

                    <input type="hidden" id="head_qc_form_mode" value="<?= $headQcFormMode; ?>">

                    <table class="mbot5" id="stack_qc_table">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Item</th>
                          <th>Chamber</th>
                          <th>Stack</th>
                          <th>Lot</th>
                          <th>Weight(KG)</th>
                          <th>Bag Qty</th>
                          <th>QC Values</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody id="stack_qc_tbody">

                        <?php if (!empty($sqRows)): ?>
                          <?php foreach ($sqRows as $ri => $sqRow):
                            $sqRow         = is_array($sqRow) ? $sqRow : (array) $sqRow;
                            $savedItemId   = htmlspecialchars($sqRow['item_id'] ?? '');
                            $savedChamberId= $sqRow['chamber'] ?? '';
                            $savedStackId  = $sqRow['stack']   ?? '';
                            $savedLotId    = $sqRow['lot']     ?? '';
                            $savedWeight   = htmlspecialchars($sqRow['weight']  ?? '');
                            $savedBagQty   = htmlspecialchars($sqRow['bag_qty'] ?? '');
                            $savedRowDbId  = htmlspecialchars($sqRow['id']      ?? '');
                            $savedQcArr    = isset($sqRow['qc']) && is_array($sqRow['qc']) ? $sqRow['qc'] : [];
                            ?>
                            <tr data-row="<?= $ri; ?>"
                                data-saved-chamber="<?= htmlspecialchars($savedChamberId); ?>"
                                data-saved-stack="<?= htmlspecialchars($savedStackId); ?>"
                                data-saved-lot="<?= htmlspecialchars($savedLotId); ?>"
                                data-row-db-id="<?= $savedRowDbId; ?>">
                              <td class="text-center row-num"><?= $ri + 1; ?></td>
                              <td>
                                <select name="item_id[]" class="form-control item-select-dropdown sq-item-select" style="min-width:130px;">
                                  <option value="" disabled>-- Select Item --</option>
                                  <?php foreach ($itemList as $itm): ?>
                                    <option value="<?= htmlspecialchars($itm['ItemID']); ?>"
                                      data-name="<?= htmlspecialchars($itm['item_name']); ?>"
                                      <?= ($savedItemId == $itm['ItemID']) ? 'selected' : ''; ?>>
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
                                <div style="display:flex; align-items:center; gap:4px;">
                                  <input type="text" name="weight[]" class="form-control"
                                    value="<?= $savedWeight; ?>" style="min-width:70px;">
                                </div>
                              </td>
                              <td><input type="text" name="bag_qty[]" class="form-control" value="<?= $savedBagQty; ?>"></td>
                              <td style="white-space:nowrap;">
                                <input type="text" class="form-control qc-display-input" name="qc[]" value=""
                                  data-saved-qc=""
                                  data-qc-json="<?= htmlspecialchars(json_encode($savedQcArr), ENT_QUOTES); ?>">
                                <div class="qc-cell-inner">
                                  <button type="button" class="btn btn-info btn-xs sq-item-info-btn"
                                    title="View / Fill QC Details" style="margin-left:3px; align-self:flex-start;">
                                    <i class="fa fa-info-circle"></i>
                                  </button>
                                </div>
                              </td>
                              <td style="width:60px; text-align:center;">
                                <button type="button" class="btn btn-success btn-xs sq-add-row" title="Add Row"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-danger btn-xs sq-remove-row" title="Remove Row"><i class="fa fa-minus"></i></button>
                              </td>
                            </tr>
                          <?php endforeach; ?>

                        <?php else: ?>
                          <tr data-row="0" data-saved-chamber="" data-saved-stack="" data-saved-lot="" data-row-db-id="">
                            <td class="text-center row-num">1</td>
                            <td>
                              <select name="item_id[]" class="form-control item-select-dropdown sq-item-select" style="min-width:130px;">
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
                              <input type="text" class="form-control qc-display-input" name="qc[]" value=""
                                data-saved-qc="" data-qc-json="[]">
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
              </div>
              <!-- ===== END CENTER QC ===== -->

              <!-- ===== DEDUCTION MATRIX ===== -->
              <div class="col-md-12 mbot5" style="margin-top:20px;">

                <h4 class="bold p_style" style="display:inline-block; margin-right:10px;">Deduction Matrix:</h4>
                <button type="button" style="float: right; margin-top: -10px" class="btn btn-warning btn-sm" id="carry_forward_btn" title="Copy Centre QC values to Head QC and Save">
                  <i class="fa fa-forward"></i> Carry Forward
                </button>

                <hr class="hr_style" />

                <table class="dm-right-table" id="deduction_matrix_table">
                  <thead>
                    <tr>
                      <th style="width:20%;">Parameter</th>
                      <th style="width:7%;">Min</th>
                      <th style="width:7%;">Max</th>
                      <th style="width:7%;">Base</th>
                      <th style="width:9%;">Calc By</th>
                      <th style="width:9%;">Centre QC</th>
                      <th style="width:12%;">Amount</th>
                      <th style="width:10%;">Head QC</th>
                    </tr>
                  </thead>
                  <tbody id="deduction_matrix_tbody">
                    <tr style="text-align:center; color:#999; padding:10px !important;">
                      <td colspan="8">Loading deduction data...</td>
                    </tr>
                  </tbody>
                </table>

                <!-- SUMMARY -->
                <table class="dm-right-table mbot5" style="margin-top:15px;">
                  <tbody>
                    <tr class="dm-summary-row">
                      <td colspan="4"><b>Total Deduction:</b></td>
                      <td colspan="4" style="text-align:right;"><b id="dm_total_deduction_val">-</b></td>
                    </tr>
                    <tr class="dm-summary-row">
                      <td colspan="4"><b>Bag Weight:</b></td>
                      <td colspan="4" style="text-align:right;"><b id="dm_bag_weight_val">-</b></td>
                    </tr>
                    <tr class="dm-summary-row">
                      <td colspan="4"><b>Net Amount:</b></td>
                      <td colspan="4" style="text-align:right;"><b id="dm_net_amount_val">-</b></td>
                    </tr>
                  </tbody>
                </table>

                <button type="button" class="btn btn-success" id="advance_payment_btn">
                  <i class="fa fa-money"></i> ADVANCE PAYMENT
                </button>
              </div>
              <!-- ===== END DEDUCTION MATRIX ===== -->

            </div><!-- end row -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===== ITEM DETAIL POPUP MODAL ===== -->
<div class="modal fade" id="itemDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width:860px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fa fa-info-circle"></i> &nbsp;
          <span id="itemModalTitle">Item Details</span>
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
                <th>value QC</th>
                <th>Matched Deduction</th>
                <th>Final Amount</th>
              </tr>
            </thead>
            <tbody id="itemQCTableBody">
              <tr>
                <td colspan="9" class="text-center" style="padding:8px !important;">
                  Select an item to load QC parameters...
                </td>
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
        <button type="button" class="btn btn-success btn-sm" id="itemModalSaveQCBtn">
          <i class="fa fa-save"></i> Save QC Values
        </button>
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

  var chamberList  = <?= json_encode($chamberList); ?>;
  var itemDataList = <?= json_encode($itemList); ?>;
  var itemDataMap  = {};
  itemDataList.forEach(function(item) { itemDataMap[item.ItemID] = item; });

  var qcValuesStore        = <?= json_encode($qcValuesStorePhp); ?>;
  var modalQCData          = {};
  var currentModalRowIndex = null;
  var currentModalItemID   = null;

  // headQCStore: stores Head QC input values keyed as "rowIdx_paramId" => { value, deduction, amount }
  var headQCStore = {};

  // deductionMatrixStore: caches deduction matrix data keyed as "rowIdx_paramId" => { matrix, calcBy, basicRate }
  var deductionMatrixStore = {};

  function escHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function buildQCDisplayString(rowIndex) {
    var stored = qcValuesStore[rowIndex];
    if (!stored || Object.keys(stored).length === 0) return '';
    var parts = [];
    $.each(stored, function(paramId, vals) {
      if (vals.pct !== '' && vals.pct !== undefined && vals.pct !== null) {
        var label = vals.paramName ? vals.paramName : ('Param#' + paramId);
        parts.push(label + ': ' + vals.pct);
      }
    });
    return parts.length === 0 ? '' : '[' + parts.join('], [') + ']';
  }

  function updateQCBadgesWithParamNames(rowIdx) {
    var $badgeWrap = $('#qc_badges_' + rowIdx);
    var stored     = qcValuesStore[rowIdx] || {};
    $badgeWrap.empty();
    var hasData = false;
    $.each(stored, function(paramId, vals) {
      if (vals.pct === '' || vals.pct === undefined || vals.pct === null) return;
      hasData = true;
      $badgeWrap.append(
        '<span class="qc-badge" data-param-id="' + escHtml(String(paramId)) + '">'
        + escHtml(vals.paramName ? vals.paramName : ('Param#' + paramId))
        + ': <b>' + escHtml(String(vals.pct)) + '</b></span>'
      );
    });
    if (!hasData) $badgeWrap.append('<span class="qc-badge-empty">-- No QC --</span>');
    var $row = $('#stack_qc_tbody tr[data-row="' + rowIdx + '"]');
    $row.find('.qc-display-input').val(buildQCDisplayString(rowIdx));
  }

  function buildStackDropdownHtml(stacks, selectedId) {
    selectedId = String(selectedId || '');
    var html = '<select name="stack[]" class="form-control sq-stack-select">'
      + '<option value="" disabled ' + (selectedId === '' ? 'selected' : '') + '>-- Select Stack --</option>';
    if (stacks && stacks.length > 0) {
      stacks.forEach(function(s) {
        var isSel = (String(s.id) === selectedId) ? 'selected' : '';
        html += '<option value="' + escHtml(String(s.id)) + '" ' + isSel + '>' + escHtml(s.StackName) + '</option>';
      });
    }
    html += '</select>';
    return html;
  }

  function buildLotDropdownHtml(lots, selectedId) {
    selectedId = String(selectedId || '');
    var html = '<select name="lot[]" class="form-control sq-lot-select">'
      + '<option value="" disabled ' + (selectedId === '' ? 'selected' : '') + '>-- Select Lot --</option>';
    if (lots && lots.length > 0) {
      lots.forEach(function(l) {
        var isSel = (String(l.id) === selectedId) ? 'selected' : '';
        html += '<option value="' + escHtml(String(l.id)) + '" ' + isSel + '>'
          + escHtml(l.LotName || l.lot_name || l.LotCode || String(l.id)) + '</option>';
      });
    }
    html += '</select>';
    return html;
  }

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
      success: function(res) {
        var stacks = [];
        if (Array.isArray(res)) stacks = res;
        else if (res.success && Array.isArray(res.data)) stacks = res.data;
        else if (Array.isArray(res.data)) stacks = res.data;
        $row.find('.stack-td').html(buildStackDropdownHtml(stacks, selectedStackId));
        var currentStackId = $row.find('.sq-stack-select').val();
        if (currentStackId) loadLotsForRow($row, currentStackId, selectedLotId);
        else $row.find('.lot-td').html(buildLotDropdownHtml([], ''));
      },
      error: function() {
        $row.find('.stack-td').html(buildStackDropdownHtml([], ''));
        $row.find('.lot-td').html(buildLotDropdownHtml([], ''));
      }
    });
  }

  function loadLotsForRow($row, stackID, selectedLotId) {
    if (!stackID) { $row.find('.lot-td').html(buildLotDropdownHtml([], '')); return; }
    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    $row.find('.lot-td').html('<select name="lot[]" class="form-control sq-lot-select"><option value="" disabled selected>Loading...</option></select>');
    $.ajax({
      url: '<?= admin_url('purchase/Inwards/LotListByStack'); ?>',
      type: 'POST',
      data: { stack_id: stackID, '<?= $this->security->get_csrf_token_name(); ?>': csrfToken },
      dataType: 'json',
      success: function(res) {
        var lots = [];
        if (Array.isArray(res)) lots = res;
        else if (res.success && Array.isArray(res.data)) lots = res.data;
        else if (Array.isArray(res.data)) lots = res.data;
        $row.find('.lot-td').html(buildLotDropdownHtml(lots, selectedLotId));
      },
      error: function() { $row.find('.lot-td').html(buildLotDropdownHtml([], '')); }
    });
  }

  // ======================== PAGE LOAD: initialize stack dropdowns and load saved QC data ========================
  $(document).ready(function() {
    $('#stack_qc_tbody tr').each(function() {
      var $row       = $(this);
      var chamberVal = $row.find('select[name="chamber[]"]').val();
      var savedStack = $row.data('saved-stack') || '';
      var savedLot   = $row.data('saved-lot')   || '';
      if (chamberVal) loadStacksForRow($row, chamberVal, savedStack, savedLot);
    });
    loadSavedQCData();
  });

  function loadSavedQCData() {
    var itemsToFetch   = {};
    var fetchCount     = 0;
    var completedCount = 0;

    $('#stack_qc_tbody tr').each(function() {
      var rowIdx = parseInt($(this).data('row'));
      var itemID = $(this).find('.sq-item-select').val();
      if (!itemID) return;
      if (!itemsToFetch[itemID]) itemsToFetch[itemID] = [];
      itemsToFetch[itemID].push(rowIdx);
    });

    fetchCount = Object.keys(itemsToFetch).length;
    if (fetchCount === 0) { rebuildDeductionMatrixTable(); return; }

    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

    $.each(itemsToFetch, function(itemID, rowIdxList) {
      $.ajax({
        url: '<?= admin_url('purchase/Inwards/ItemQCList'); ?>',
        type: 'POST',
        data: { ItemID: itemID, '<?= $this->security->get_csrf_token_name(); ?>': csrfToken },
        dataType: 'json',
        success: function(res) {
          var qcRows = [];
          if (Array.isArray(res)) qcRows = res;
          else if (res.success == true && Array.isArray(res.data)) qcRows = res.data;
          else if (Array.isArray(res.data)) qcRows = res.data;

          var paramIdMap = {};
          qcRows.forEach(function(r) { paramIdMap[String(r.id)] = r; });

          rowIdxList.forEach(function(rowIdx) {
            modalQCData[rowIdx] = qcRows;
            var $row     = $('#stack_qc_tbody tr[data-row="' + rowIdx + '"]');
            var qcJson   = $row.find('.qc-display-input').data('qc-json') || [];
            if (typeof qcJson === 'string') { try { qcJson = JSON.parse(qcJson); } catch(e) { qcJson = []; } }

            var item      = itemDataMap[itemID] || {};
            var basicRate = parseFloat(item.BasicRate) || 0;
            if (!qcValuesStore[rowIdx]) qcValuesStore[rowIdx] = {};

            qcJson.forEach(function(qcEntry) {
              qcEntry = (typeof qcEntry === 'object') ? qcEntry : {};
              var paramId    = String(qcEntry.parameter_id || '');
              var paramVal   = String(qcEntry.value        || '');
              var dedAmt     = parseFloat(qcEntry.deductionamt) || 0;
              var qcDetailId = String(qcEntry.id           || '');
              var hvalue     = String(qcEntry.hvalue       || '');
              if (!paramId) return;

              var qcRowData    = paramIdMap[paramId] || null;
              var paramName    = qcRowData ? (qcRowData.ParameterName || '') : ('Param#' + paramId);
              var calcBy       = qcRowData ? (qcRowData.CalculationBy  || '') : '';
              var minVal       = qcRowData ? (qcRowData.MinValue  || '-') : '-';
              var maxVal       = qcRowData ? (qcRowData.MaxValue  || '-') : '-';
              var baseVal      = qcRowData ? (qcRowData.BaseValue || '-') : '-';
              var deduction    = qcRowData ? findDeduction(paramVal, qcRowData.deduction_matrix) : null;
              var reductionAmt = (deduction !== null && basicRate > 0)
                ? calcReductionAmount(calcBy, basicRate, deduction) : dedAmt;

              qcValuesStore[rowIdx][paramId] = {
                pct: paramVal, paramName, parameter_id: paramId,
                qcDetailId, hvalue,
                deduction, calcBy, reductionAmt, basicRate,
                minVal, maxVal, baseVal
              };

              if (hvalue !== '') {
                var storeKey      = rowIdx + '_' + paramId;
                var headDeduction = qcRowData ? findDeduction(hvalue, qcRowData.deduction_matrix) : null;
                var headRedAmt    = 0;
                if (headDeduction !== null && basicRate > 0) {
                  headRedAmt = calcReductionAmount(calcBy, basicRate, headDeduction);
                } else if (headDeduction !== null) {
                  headRedAmt = parseFloat(headDeduction) || 0;
                }
                headQCStore[storeKey] = {
                  value     : hvalue,
                  deduction : headDeduction,
                  amount    : headRedAmt,
                  calcBy    : calcBy,
                  basicRate : basicRate
                };
              }

              if (qcRowData && qcRowData.deduction_matrix) {
                var storeKey2 = rowIdx + '_' + paramId;
                deductionMatrixStore[storeKey2] = {
                  matrix    : qcRowData.deduction_matrix,
                  calcBy    : calcBy,
                  basicRate : basicRate
                };
              }
            });
            updateQCBadgesWithParamNames(rowIdx);
          });

          completedCount++;
          if (completedCount === fetchCount) setTimeout(function() { rebuildDeductionMatrixTable(); }, 100);
        },
        error: function() {
          completedCount++;
          if (completedCount === fetchCount) setTimeout(function() { rebuildDeductionMatrixTable(); }, 100);
        }
      });
    });
  }

  function findDeduction(qcValue, deductionMatrix) {
    if (!deductionMatrix || deductionMatrix.length === 0) return null;
    var qv = parseFloat(qcValue);
    if (isNaN(qv)) return null;
    var sorted = deductionMatrix.slice().sort(function(a,b) { return parseFloat(a.Value) - parseFloat(b.Value); });
    for (var i = 0; i < sorted.length; i++) {
      if (parseFloat(sorted[i].Value) === qv) return parseFloat(sorted[i].Deduction);
    }
    if (qv < parseFloat(sorted[0].Value)) return parseFloat(sorted[0].Deduction);
    if (qv > parseFloat(sorted[sorted.length-1].Value)) return parseFloat(sorted[sorted.length-1].Deduction);
    for (var i = 0; i < sorted.length - 1; i++) {
      var v1 = parseFloat(sorted[i].Value),    v2 = parseFloat(sorted[i+1].Value);
      var d1 = parseFloat(sorted[i].Deduction), d2 = parseFloat(sorted[i+1].Deduction);
      if (qv >= v1 && qv <= v2) {
        return Math.round((d1 + ((qv-v1)/(v2-v1))*(d2-d1)) * 10000) / 10000;
      }
    }
    return parseFloat(sorted[sorted.length-1].Deduction);
  }

  function getMatchedMatrixValue(qcValue, deductionMatrix) {
    if (!deductionMatrix || deductionMatrix.length === 0) return null;
    var qv = parseFloat(qcValue);
    if (isNaN(qv)) return null;
    var sorted = deductionMatrix.slice().sort(function(a,b) { return parseFloat(a.Value)-parseFloat(b.Value); });
    for (var i = 0; i < sorted.length; i++) {
      if (parseFloat(sorted[i].Value) === qv) return parseFloat(sorted[i].Value);
    }
    if (qv < parseFloat(sorted[0].Value)) return parseFloat(sorted[0].Value);
    if (qv > parseFloat(sorted[sorted.length-1].Value)) return parseFloat(sorted[sorted.length-1].Value);
    for (var i = 0; i < sorted.length-1; i++) {
      var v1 = parseFloat(sorted[i].Value), v2 = parseFloat(sorted[i+1].Value);
      if (qv > v1 && qv < v2) return v1;
    }
    return null;
  }

  function calcReductionAmount(calculationBy, basicRate, deduction) {
    var br = parseFloat(basicRate) || 0, ded = parseFloat(deduction) || 0;
    if (calculationBy && calculationBy.toLowerCase() === 'percentage') return (br * ded / 100);
    return ded;
  }

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

  function rebuildDeductionMatrixTable() {
    var tbody = $('#deduction_matrix_tbody');
    tbody.empty();

    var totalWeight = 0, totalBagQty = 0, totalfWeight = 0, total = 0;
    $('#stack_qc_tbody tr').each(function() {
      var w = parseFloat($(this).find('input[name="weight[]"]').val()) || 0;
      var b = parseFloat($(this).find('input[name="bag_qty[]"]').val()) || 0;
      totalWeight += w; totalBagQty += b;
      totalfWeight += (w / b);
      total += totalfWeight * totalBagQty;
    });
    $('#dm_bag_weight_val').text((totalWeight > 0 || totalBagQty > 0) ? total.toFixed(2) + ' KG' : '-');

    var rowDataList = [];
    var hasAnyData  = false;

    $.each(qcValuesStore, function(rowIdx, params) {
      var $row      = $('#stack_qc_tbody tr[data-row="' + rowIdx + '"]');
      var itemID    = $row.find('.sq-item-select').val() || '';
      var item      = itemDataMap[itemID] || {};
      var itemName  = item.item_name || itemID || ('Row ' + (parseInt(rowIdx)+1));
      var basicRate = parseFloat(item.BasicRate) || 0;
      var qcRows    = modalQCData[rowIdx] || [];
      var paramIdMap= {};
      qcRows.forEach(function(r) { paramIdMap[String(r.id)] = r; });

      var rowParams = [];
      $.each(params, function(paramId, vals) {
        if (!vals.pct || vals.pct === '') return;
        hasAnyData = true;
        var paramName   = vals.paramName || ('Param#' + paramId);
        var calcBy      = vals.calcBy || '';
        var deduction   = vals.deduction;
        var amount      = (vals.reductionAmt !== null && vals.reductionAmt !== undefined)
          ? parseFloat(vals.reductionAmt) : 0;
        var minVal      = vals.minVal  || '-';
        var maxVal      = vals.maxVal  || '-';
        var baseVal     = vals.baseVal || '-';
        var qcDetailId  = vals.qcDetailId || '';
        var hvalue      = vals.hvalue  || '';
        var deductionMatrix = [];

        if ((deduction === null || deduction === undefined) || amount === 0) {
          var qcRowData = paramIdMap[String(paramId)] || null;
          if (qcRowData) {
            calcBy    = qcRowData.CalculationBy || '';
            minVal    = qcRowData.MinValue  || '-';
            maxVal    = qcRowData.MaxValue  || '-';
            baseVal   = qcRowData.BaseValue || '-';
            deduction = findDeduction(vals.pct, qcRowData.deduction_matrix);
            if (deduction !== null && basicRate > 0) amount = calcReductionAmount(calcBy, basicRate, deduction);
            deductionMatrix = qcRowData.deduction_matrix || [];
          }
        } else {
          var qcRowData2 = paramIdMap[String(paramId)] || null;
          if (qcRowData2) deductionMatrix = qcRowData2.deduction_matrix || [];
        }

        var storeKey = rowIdx + '_' + paramId;
        if (deductionMatrix.length > 0) {
          deductionMatrixStore[storeKey] = { matrix: deductionMatrix, calcBy: calcBy, basicRate: basicRate };
        }

        var headQCVal    = '';
        var headQCAmount = null;
        if (headQCStore[storeKey] !== undefined) {
          headQCVal    = headQCStore[storeKey].value || '';
          headQCAmount = headQCStore[storeKey].amount;
        } else if (hvalue !== '') {
          var headDed = findDeduction(hvalue, deductionMatrix);
          var headAmt = 0;
          if (headDed !== null && basicRate > 0) headAmt = calcReductionAmount(calcBy, basicRate, headDed);
          else if (headDed !== null) headAmt = parseFloat(headDed) || 0;
          headQCStore[storeKey] = { value: hvalue, deduction: headDed, amount: headAmt, calcBy: calcBy, basicRate: basicRate };
          headQCVal    = hvalue;
          headQCAmount = headAmt;
        }

        var displayAmount = (headQCAmount !== null && headQCAmount !== undefined) ? headQCAmount : amount;

        rowParams.push({
          paramId, paramName,
          qcDetailId, hvalue,
          centreQCValue : parseFloat(vals.pct) || 0,
          deduction, calcBy,
          centreAmount  : amount,
          headQCVal,
          displayAmount : displayAmount || 0,
          minVal, maxVal, baseVal,
          storeKey,
          deductionMatrix
        });
      });

      if (rowParams.length > 0) rowDataList.push({ rowIdx: parseInt(rowIdx), itemID, itemName, basicRate, params: rowParams });
    });

    var totalAllDeductions = 0, totalNetAmount = 0;

    if (!hasAnyData) {
      tbody.html('<tr style="text-align:center;color:#999;"><td colspan="8">No QC data found...</td></tr>');
      $('#dm_total_deduction_val').text('-');
      $('#dm_net_amount_val').text('-');
      return;
    }

    rowDataList.forEach(function(rData) {
      tbody.append(
        '<tr class="dm-item-header-row"><td colspan="8">'
        + '<i class="fa fa-cube"></i> <b>' + escHtml(rData.itemName) + '</b>'
        + ' <small style="color:#95a5a6; font-weight:normal; margin-left:6px;">Row #' + (rData.rowIdx+1)
        + ' &nbsp;|&nbsp; Base Rate: ₹' + parseFloat(rData.basicRate).toFixed(2) + '/KG</small>'
        + '</td></tr>'
      );

      var rowTotalReduction = 0;

      rData.params.forEach(function(pData) {
        var displayAmount = pData.displayAmount || 0;
        var calcByLabel   = pData.calcBy ? pData.calcBy : '-';

        var matrixJson = '';
        if (pData.deductionMatrix && pData.deductionMatrix.length > 0) {
          matrixJson = JSON.stringify(pData.deductionMatrix).replace(/"/g, '&quot;');
        }

        if (displayAmount > 0) {
          rowTotalReduction  += displayAmount;
          totalAllDeductions += displayAmount;
        }

        var amountCellId = 'dm_amount_' + escHtml(pData.storeKey);

        tbody.append(
          '<tr class="dm-param-row"'
          + ' data-store-key="'    + escHtml(pData.storeKey)           + '"'
          + ' data-parameter-id="' + escHtml(String(pData.paramId))    + '"'
          + ' data-qc-detail-id="' + escHtml(String(pData.qcDetailId)) + '">'
          + '<td>' + escHtml(pData.paramName) + '</td>'
          + '<td style="text-align:right;">' + escHtml(String(pData.minVal)) + '</td>'
          + '<td style="text-align:right;">' + escHtml(String(pData.maxVal)) + '</td>'
          + '<td style="text-align:right;">' + escHtml(String(pData.baseVal)) + '</td>'
          + '<td style="text-align:center;"><span style="font-size:10px;background:#e8f4fd;border:1px solid #5bc0de;border-radius:3px;padding:1px 4px;">'
          + escHtml(calcByLabel) + '</span></td>'
          + '<td style="text-align:right;">' + parseFloat(pData.centreQCValue).toFixed(4) + '</td>'
          + '<td class="dm-amount-cell' + (pData.headQCVal !== '' ? ' dm-amount-updated' : '') + '" id="' + amountCellId + '" style="text-align:right;">'
          +   '<span class="amount-value">' + (displayAmount > 0 ? displayAmount.toFixed(4) : '-') + '</span>'
          + '</td>'
          + '<td style="text-align:center;">'
          +   '<input type="text" step="any" min="0"'
          +   ' class="form-control dm-head-qc-input"'
          +   ' data-store-key="'       + escHtml(pData.storeKey)           + '"'
          +   ' data-calc-by="'         + escHtml(pData.calcBy)             + '"'
          +   ' data-basic-rate="'      + escHtml(String(rData.basicRate))  + '"'
          +   ' data-deduction-matrix="' + matrixJson                       + '"'
          +   ' data-amount-cell-id="'  + amountCellId                      + '"'
          +   ' oninput="this.value = this.value.replace(/[^0-9.]/g, \'\')"'
          +   ' value="' + (pData.headQCVal !== '' ? escHtml(String(pData.headQCVal)) : '') + '"'
          +   ' placeholder="0.00">'
          + '</td>'
          + '</tr>'
        );
      });

      tbody.append(
        '<tr class="dm-item-total"><td colspan="4"><b>' + escHtml(rData.itemName) + ' - Total Deduction</b></td>'
        + '<td colspan="4" style="text-align:right;" id="dm_row_total_' + rData.rowIdx + '"><b>₹' + rowTotalReduction.toFixed(4) + '</b></td></tr>'
      );

      var finalRate = rData.basicRate > 0 ? (rData.basicRate - rowTotalReduction) : 0;
      tbody.append(
        '<tr class="dm-final-rate-row"><td colspan="4"><b>Final Rate/KG - ' + escHtml(rData.itemName) + '</b></td>'
        + '<td colspan="4" style="text-align:right;" id="dm_final_rate_' + rData.rowIdx + '"><b>₹' + finalRate.toFixed(4) + '</b></td></tr>'
      );
      totalNetAmount += finalRate;
    });

    $('#dm_total_deduction_val').text(totalAllDeductions.toFixed(4));
    $('#dm_net_amount_val').text(totalNetAmount.toFixed(4));
  }

  // =====================================================================
  // HEAD QC INPUT EVENT HANDLER
  // =====================================================================
  $(document).on('input change', '.dm-head-qc-input', function() {
    var $input        = $(this);
    var storeKey      = $input.data('store-key');
    var calcBy        = $input.data('calc-by') || '';
    var basicRate     = parseFloat($input.data('basic-rate')) || 0;
    var amountCellId  = $input.data('amount-cell-id');
    var headQCVal     = $input.val().trim();
    var $amountCell   = $('#' + amountCellId);

    var matrixRaw = $input.attr('data-deduction-matrix') || '';
    var deductionMatrix = [];
    if (matrixRaw) {
      try {
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = matrixRaw;
        deductionMatrix = JSON.parse(tempDiv.textContent || tempDiv.innerText || '[]');
      } catch(e) {
        if (deductionMatrixStore[storeKey]) {
          deductionMatrix = deductionMatrixStore[storeKey].matrix || [];
          calcBy          = deductionMatrixStore[storeKey].calcBy  || calcBy;
          basicRate       = deductionMatrixStore[storeKey].basicRate || basicRate;
        }
      }
    }
    if (deductionMatrix.length === 0 && deductionMatrixStore[storeKey]) {
      deductionMatrix = deductionMatrixStore[storeKey].matrix || [];
      calcBy          = deductionMatrixStore[storeKey].calcBy  || calcBy;
      basicRate       = deductionMatrixStore[storeKey].basicRate || basicRate;
    }

    if (headQCVal === '' || isNaN(parseFloat(headQCVal))) {
      var centreAmount = 0;
      var parts        = storeKey.split('_');
      var rowIdxPart   = parts[0];
      var paramIdPart  = parts.slice(1).join('_');
      if (qcValuesStore[rowIdxPart] && qcValuesStore[rowIdxPart][paramIdPart]) {
        centreAmount = parseFloat(qcValuesStore[rowIdxPart][paramIdPart].reductionAmt) || 0;
      }
      delete headQCStore[storeKey];
      $amountCell.removeClass('dm-amount-updated');
      $amountCell.html('<span class="amount-value">' + (centreAmount > 0 ? centreAmount.toFixed(4) : '-') + '</span>');
      recalcDeductionTotals();
      return;
    }

    var deduction    = findDeduction(headQCVal, deductionMatrix);
    var reductionAmt = 0;

    if (deduction !== null && basicRate > 0) {
      reductionAmt = calcReductionAmount(calcBy, basicRate, deduction);
    } else if (deduction !== null) {
      reductionAmt = parseFloat(deduction) || 0;
    }

    headQCStore[storeKey] = {
      value     : headQCVal,
      deduction : deduction,
      amount    : reductionAmt,
      calcBy    : calcBy,
      basicRate : basicRate
    };

    $amountCell.addClass('dm-amount-updated');
    $amountCell.html('<span class="amount-value">' + (reductionAmt > 0 ? reductionAmt.toFixed(4) : '-') + '</span>');

    recalcDeductionTotals();
  });

  // =====================================================================
  // recalcDeductionTotals
  // =====================================================================
  function recalcDeductionTotals() {
    var grandTotal    = 0;
    var grandFinalNet = 0;

    $('#deduction_matrix_tbody tr.dm-item-header-row').each(function() {
      var $headerRow = $(this);
      var rowIdx     = null;
      var rowTotal   = 0;
      var basicRate  = 0;

      var headerText = $headerRow.text();
      var rowMatch   = headerText.match(/Row #(\d+)/);
      if (rowMatch) rowIdx = parseInt(rowMatch[1]) - 1;

      var rateMatch = headerText.match(/Base Rate: ₹([\d.]+)/);
      if (rateMatch) basicRate = parseFloat(rateMatch[1]) || 0;

      var $nextRows = $headerRow.nextUntil('.dm-item-header-row');
      $nextRows.filter('.dm-param-row').each(function() {
        var amtText = $(this).find('.dm-amount-cell .amount-value').text().trim();
        var amt     = parseFloat(amtText) || 0;
        rowTotal   += amt;
      });

      grandTotal += rowTotal;

      if (rowIdx !== null) {
        $('#dm_row_total_' + rowIdx).html('<b>₹' + rowTotal.toFixed(4) + '</b>');
        var finalRate = basicRate > 0 ? (basicRate - rowTotal) : 0;
        $('#dm_final_rate_' + rowIdx).html('<b>₹' + finalRate.toFixed(4) + '</b>');
        grandFinalNet += finalRate;
      }
    });

    $('#dm_total_deduction_val').text(grandTotal.toFixed(4));
    $('#dm_net_amount_val').text(grandFinalNet.toFixed(4));
  }

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

    var $matchedCell = $tr.find('.qc-matched-deduction');
    var $finalCell   = $tr.find('.qc-final-amount');

    if (qcVal === '' || isNaN(parseFloat(qcVal))) {
      $matchedCell.html('<span style="color:#aaa;">-</span>');
      $finalCell.html('<span style="color:#aaa;">-</span>');
      renderDeductionMatrixTable(paramName, qcRowData.deduction_matrix, null);
      rebuildDeductionMatrixTable();
      return;
    }

    var currentVal     = parseFloat(qcVal) || 0;
    var deductionOnVal = findDeduction(currentVal, qcRowData.deduction_matrix);

    if (deductionOnVal === null) {
      $matchedCell.html('<span style="color:#aaa;">-</span>');
      $finalCell.html('<span style="color:#aaa;">-</span>');
      renderDeductionMatrixTable(paramName, qcRowData.deduction_matrix, currentVal);
      rebuildDeductionMatrixTable();
      return;
    }

    var reductionAmt = calcReductionAmount(calcBy, basicRate, deductionOnVal);
    var dedDisplay   = parseFloat(deductionOnVal).toFixed(2) + (calcBy.toLowerCase() === 'percentage' ? '%' : '');
    var matchedVal   = getMatchedMatrixValue(currentVal, qcRowData.deduction_matrix);
    var isExact      = (matchedVal !== null && parseFloat(matchedVal) === parseFloat(currentVal));
    var matchSuffix  = isExact ? '' : ' <small style="color:#8a6d3b;">(~' + matchedVal + ')</small>';

    $matchedCell.html('<span class="qc-calc-badge"><i class="fa fa-arrow-down"></i> ' + escHtml(dedDisplay) + '</span>' + matchSuffix);
    $finalCell.html('<b>' + reductionAmt.toFixed(4) + '</b>');

    renderDeductionMatrixTable(paramName, qcRowData.deduction_matrix, currentVal);
    rebuildDeductionMatrixTable();
  }

  function saveCurrentQCValues() {
    if (currentModalRowIndex === null) return;
    if (!qcValuesStore[currentModalRowIndex]) qcValuesStore[currentModalRowIndex] = {};

    var qcRows    = modalQCData[currentModalRowIndex] || [];
    var item      = itemDataMap[currentModalItemID] || {};
    var basicRate = parseFloat(item.BasicRate) || 0;

    $('#itemQCTableBody tr').each(function() {
      var $tr       = $(this);
      var paramIdx  = parseInt($tr.data('param-idx'));
      var qcRowData = qcRows[paramIdx];
      if (!qcRowData) return;

      var pctInput = $tr.find('.qc-Percentage_Wise-input');
      if (!pctInput.length) return;

      var nameAttr  = pctInput.attr('name') || '';
      var paramName = pctInput.data('param-name') || '';
      var match     = nameAttr.match(/\[(\w+)\]/);
      if (!match || !match[1]) return;

      var paramId      = String(match[1]);
      var qcVal        = pctInput.val();
      var deduction    = findDeduction(qcVal, qcRowData.deduction_matrix);
      var calcBy       = qcRowData.CalculationBy || '';
      var minVal       = qcRowData.MinValue  || '-';
      var maxVal       = qcRowData.MaxValue  || '-';
      var baseVal      = qcRowData.BaseValue || '-';
      var reductionAmt = (deduction !== null && basicRate > 0) ? calcReductionAmount(calcBy, basicRate, deduction) : null;

      var existingEntry      = qcValuesStore[currentModalRowIndex][paramId] || {};
      var existingQcDetailId = existingEntry.qcDetailId || '';
      var existingHvalue     = existingEntry.hvalue     || '';

      qcValuesStore[currentModalRowIndex][paramId] = {
        pct: qcVal, paramName, parameter_id: paramId,
        qcDetailId   : existingQcDetailId,
        hvalue       : existingHvalue,
        deduction, calcBy, reductionAmt, basicRate,
        minVal, maxVal, baseVal
      };

      var storeKey = currentModalRowIndex + '_' + paramId;
      if (qcRowData.deduction_matrix && qcRowData.deduction_matrix.length > 0) {
        deductionMatrixStore[storeKey] = { matrix: qcRowData.deduction_matrix, calcBy: calcBy, basicRate: basicRate };
      }
    });

    updateQCBadgesWithParamNames(currentModalRowIndex);
    rebuildDeductionMatrixTable();
  }

  function restoreQCValues(rowIndex, qcRows) {
    var savedVals = qcValuesStore[rowIndex] || {};
    if (Object.keys(savedVals).length === 0) return;

    var paramIdToVal = {};
    $.each(savedVals, function(paramId, vals) { paramIdToVal[String(paramId)] = vals.pct || ''; });

    var item      = itemDataMap[currentModalItemID] || {};
    var basicRate = parseFloat(item.BasicRate) || 0;

    $('#itemQCTableBody tr').each(function() {
      var $tr      = $(this);
      var pctInput = $tr.find('.qc-Percentage_Wise-input');
      if (!pctInput.length) return;
      var nameAttr = pctInput.attr('name') || '';
      var match    = nameAttr.match(/\[(\w+)\]/);
      if (!match || !match[1]) return;
      var paramId  = String(match[1]);
      if (paramIdToVal.hasOwnProperty(paramId) && paramIdToVal[paramId] !== '') {
        pctInput.val(paramIdToVal[paramId]);
        recalcModalRow($tr, qcRows, currentModalItemID);
      }
    });

    var freshStore = {};
    $('#itemQCTableBody tr').each(function() {
      var $tr      = $(this);
      var paramIdx = parseInt($tr.data('param-idx'));
      var pctInput = $tr.find('.qc-Percentage_Wise-input');
      if (!pctInput.length) return;
      var nameAttr = pctInput.attr('name') || '';
      var match    = nameAttr.match(/\[(\w+)\]/);
      if (!match || !match[1]) return;
      var paramId        = String(match[1]);
      var paramName      = pctInput.data('param-name') || '';
      var qcRowData      = qcRows[paramIdx] || null;
      var qcVal          = pctInput.val();
      var deduction      = qcRowData ? findDeduction(qcVal, qcRowData.deduction_matrix) : null;
      var calcBy         = qcRowData ? (qcRowData.CalculationBy || '') : '';
      var minVal         = qcRowData ? (qcRowData.MinValue  || '-') : '-';
      var maxVal         = qcRowData ? (qcRowData.MaxValue  || '-') : '-';
      var baseVal        = qcRowData ? (qcRowData.BaseValue || '-') : '-';
      var redAmt         = (deduction !== null && basicRate > 0)
        ? calcReductionAmount(calcBy, basicRate, deduction)
        : (savedVals[paramId] ? (parseFloat(savedVals[paramId].reductionAmt) || null) : null);
      var existingQcDetailId = (savedVals[paramId] || {}).qcDetailId || '';
      var existingHvalue     = (savedVals[paramId] || {}).hvalue     || '';

      freshStore[paramId] = {
        pct: qcVal, paramName, parameter_id: paramId,
        qcDetailId   : existingQcDetailId,
        hvalue       : existingHvalue,
        deduction, calcBy, reductionAmt: redAmt, basicRate,
        minVal, maxVal, baseVal
      };
    });
    qcValuesStore[rowIndex] = freshStore;
  }

  $('#itemDetailModal').on('hide.bs.modal', function() {
    saveCurrentQCValues();
    currentModalRowIndex = null;
    currentModalItemID   = null;
    $('#savedQcDisplay').hide();
    $('#savedQcText').text('');
    $('#deductionMatrixSection').hide();
  });

  $(document).on('click', '#itemModalSaveQCBtn', function() {
    saveCurrentQCValues();
    toastr.success('QC values saved successfully!');
    $('#itemDetailModal').modal('hide');
  });

  $(document).on('input change', '#itemQCTableBody .qc-Percentage_Wise-input', function() {
    var $tr    = $(this).closest('tr');
    var qcRows = modalQCData[currentModalRowIndex] || [];
    recalcModalRow($tr, qcRows, currentModalItemID);
  });

  $(document).on('keypress', '.qc-Percentage_Wise-input', function(e) {
    var char = String.fromCharCode(e.which);
    var val  = $(this).val();
    if (!/[0-9.]/.test(char)) { e.preventDefault(); return; }
    if (char === '.' && val.indexOf('.') !== -1) { e.preventDefault(); return; }
  });

  function openItemDetailPopup(rowIndex, selectedItemID) {
    saveCurrentQCValues();
    if (!selectedItemID) { toastr.warning('Please select an item first!'); return; }
    var item = itemDataMap[selectedItemID];
    if (!item) { toastr.warning('Item details not found!'); return; }

    currentModalRowIndex = rowIndex;
    currentModalItemID   = selectedItemID;

    $('#itemModalTitle').text(item.item_name || selectedItemID);
    $('#itemModalBasicRate').text('Basic Rate: ₹' + (parseFloat(item.BasicRate)||0).toFixed(2) + ' / KG');

    var storedVals   = qcValuesStore[rowIndex] || {};
    var savedQcParts = [];
    $.each(storedVals, function(paramId, vals) {
      if (vals.pct && vals.pct !== '') savedQcParts.push((vals.paramName || ('Param#'+paramId)) + ': ' + vals.pct);
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
      success: function(res) {
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
              + '<td>' + escHtml(row.MinValue       || '-') + '</td>'
              + '<td>' + escHtml(row.MaxValue       || '-') + '</td>'
              + '<td>' + escHtml(row.BaseValue      || '-') + '</td>'
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
      error: function() {
        $('#itemQCLoader').hide();
        $('#itemQCTableWrapper').show();
        $('#itemQCTableBody').html('<tr><td colspan="9" class="text-center" style="padding:8px !important;color:red;">Failed to load QC parameters!</td></tr>');
      }
    });
  }

  $(document).on('change', '.sq-item-select', function() {
    var $row      = $(this).closest('tr');
    var itemID    = $(this).val();
    var item      = itemDataMap[itemID] || {};
    var unitLabel = item.WeightUnit ? item.WeightUnit : 'KG';
    var unitWeight= item.UnitWeight  ? item.UnitWeight  : '';
    $row.find('.weight-unit-label').text(unitLabel);
    var $weightInput = $row.find('input[name="weight[]"]');
    if ($weightInput.val() === '' && unitWeight !== '') $weightInput.val(unitWeight);
    openItemDetailPopup($row.data('row'), itemID);
  });

  $(document).on('click', '.sq-item-info-btn', function() {
    var $row = $(this).closest('tr');
    openItemDetailPopup($row.data('row'), $row.find('.sq-item-select').val());
  });

  function buildItemDropdownHtml() {
    var html = '<select name="item_id[]" class="form-control item-select-dropdown sq-item-select" style="min-width:130px;">'
      + '<option value="" disabled selected>-- Select Item --</option>';
    itemDataList.forEach(function(itm) {
      html += '<option value="' + escHtml(itm.ItemID) + '" data-name="' + escHtml(itm.item_name) + '">'
        + escHtml(itm.item_name) + '</option>';
    });
    html += '</select>';
    return html;
  }

  function buildChamberDropdownHtml(selectedId) {
    selectedId = String(selectedId || '');
    var html = '<select name="chamber[]" class="form-control">'
      + '<option value="" disabled ' + (selectedId === '' ? 'selected' : '') + '>-- Select Chamber --</option>';
    chamberList.forEach(function(ch) {
      var isSel = (String(ch.id) === selectedId) ? 'selected' : '';
      html += '<option value="' + escHtml(String(ch.id)) + '" ' + isSel + '>' + escHtml(ch.ChamberName) + '</option>';
    });
    html += '</select>';
    return html;
  }

  $(document).on('change', 'select[name="chamber[]"]', function() {
    var chamberID = $(this).val();
    var $row = $(this).closest('tr');
    $row.find('.stack-td').html(buildStackDropdownHtml([], ''));
    $row.find('.lot-td').html(buildLotDropdownHtml([], ''));
    if (!chamberID) return;
    loadStacksForRow($row, chamberID, '', '');
  });

  $(document).on('change', '.sq-stack-select', function() {
    var stackID = $(this).val();
    var $row    = $(this).closest('tr');
    $row.find('.lot-td').html(buildLotDropdownHtml([], ''));
    if (!stackID) return;
    loadLotsForRow($row, stackID, '');
  });

  $(document).on('click', '.sq-add-row', function() {
    var tbody    = $('#stack_qc_tbody');
    var rowCount = tbody.find('tr').length;
    var newIdx   = rowCount;
    var newRow   = '<tr data-row="' + newIdx + '" data-saved-chamber="" data-saved-stack="" data-saved-lot="" data-row-db-id="">'
      + '<td class="text-center row-num">' + (rowCount+1) + '</td>'
      + '<td>' + buildItemDropdownHtml() + '</td>'
      + '<td>' + buildChamberDropdownHtml('') + '</td>'
      + '<td class="stack-td">' + buildStackDropdownHtml([], '') + '</td>'
      + '<td class="lot-td">'   + buildLotDropdownHtml([], '')   + '</td>'
      + '<td><div style="display:flex; align-items:center; gap:4px;">'
      + '<input type="text" name="weight[]" class="form-control" style="min-width:70px;">'
      + '<span class="weight-unit-label" style="font-size:10px; color:#31708f; background:#d9edf7; border:1px solid #5bc0de; border-radius:3px; padding:1px 5px; white-space:nowrap;">KG</span>'
      + '</div></td>'
      + '<td><input type="text" name="bag_qty[]" class="form-control"></td>'
      + '<td style="white-space:nowrap;">'
      + '<input type="text" class="form-control qc-display-input" name="qc[]" value="" data-saved-qc="" data-qc-json="[]">'
      + '<div class="qc-cell-inner">'
      + '<div class="qc-badge-wrapper" id="qc_badges_' + newIdx + '"><span class="qc-badge-empty">-- No QC --</span></div>'
      + '<button type="button" class="btn btn-info btn-xs sq-item-info-btn" title="View / Fill QC Details" style="margin-left:3px;align-self:flex-start;"><i class="fa fa-info-circle"></i></button>'
      + '</div></td>'
      + '<td style="width:60px;text-align:center;">'
      + '<button type="button" class="btn btn-success btn-xs sq-add-row" title="Add Row"><i class="fa fa-plus"></i></button>'
      + '<button type="button" class="btn btn-danger btn-xs sq-remove-row" title="Remove Row"><i class="fa fa-minus"></i></button>'
      + '</td></tr>';
    tbody.append(newRow);
    renumberStackRows();
  });

  $(document).on('click', '.sq-remove-row', function() {
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
    $('#stack_qc_tbody tr').each(function() { updateQCBadgesWithParamNames(parseInt($(this).data('row'))); });
    rebuildDeductionMatrixTable();
  }

  $('#stack_qc_form').on('submit', function(e) {
    e.preventDefault();
    saveCurrentQCValues();

    var structuredRows = [];
    $('#stack_qc_tbody tr').each(function() {
      var $tr     = $(this);
      var rowIdx  = parseInt($tr.data('row'));
      var item_id = $tr.find('select[name="item_id[]"]').val() || '';
      var chamber = $tr.find('select[name="chamber[]"]').val() || '';
      var stack   = $tr.find('select[name="stack[]"]').val()   || '';
      var lot     = $tr.find('select[name="lot[]"]').val()     || '';
      var weight  = $tr.find('input[name="weight[]"]').val()   || '';
      var bag_qty = $tr.find('input[name="bag_qty[]"]').val()  || '';

      var qcParams = [];
      var storedQC = qcValuesStore[rowIdx] || {};
      $.each(storedQC, function(paramKey, vals) {
        if (vals.pct === '' || vals.pct === undefined || vals.pct === null) return;
        qcParams.push({
          parameter_id: vals.parameter_id || paramKey,
          value: vals.pct,
          deductionamt: (vals.reductionAmt !== null && vals.reductionAmt !== undefined)
            ? parseFloat(vals.reductionAmt).toFixed(4) : '0.0000'
        });
      });
      structuredRows.push({ item_id, chamber, stack, lot, weight, bag_qty, qc: qcParams });
    });

    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();
    var postData  = {
      GateINID  : $('#sq_GateINID').val(),
      form_mode : $('#sq_form_mode').val(),
      godown    : $('#sq_gatein_id').val(),
      update_id : $('#sq_update_id').val(),
      rows_json : JSON.stringify(structuredRows)
    };
    postData['<?= $this->security->get_csrf_token_name(); ?>'] = csrfToken;

    $('#sq_update_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    $.ajax({
      url: '<?= admin_url('purchase/Inwards/SaveStackQCDetails'); ?>',
      type: 'POST', data: postData, dataType: 'json',
      success: function(res) {
        $('#sq_update_btn').prop('disabled', false).html('<i class="fa fa-save"></i> UPDATE STACK DETAILS');
        if (res.success == true) {
          toastr.success(res.message || 'Stack QC details saved successfully!');
          $('#sq_form_mode').val('edit');
          var resRows = [];
          if (Array.isArray(res.data)) resRows = res.data;
          else if (res.data && typeof res.data === 'object') resRows = [res.data];
          if (resRows.length > 0) {
            if (resRows[0] && resRows[0].id) $('#sq_update_id').val(resRows[0].id);
            var newQcValuesStore = {};
            resRows.forEach(function(resRow, ri) {
              var $tr = $('#stack_qc_tbody tr[data-row="' + ri + '"]');
              if (!$tr.length) return;
              $tr.attr('data-row-db-id', resRow.id || '');
              var newQcArr = Array.isArray(resRow.qc) ? resRow.qc : [];
              $tr.find('.qc-display-input').attr('data-qc-json', JSON.stringify(newQcArr));
              newQcValuesStore[ri] = {};
              var itemID    = $tr.find('.sq-item-select').val() || '';
              var item      = itemDataMap[itemID] || {};
              var basicRate = parseFloat(item.BasicRate) || 0;
              var qcRows    = modalQCData[ri] || [];
              var paramIdMap= {};
              qcRows.forEach(function(r) { paramIdMap[String(r.id)] = r; });
              newQcArr.forEach(function(qcEntry) {
                qcEntry = (typeof qcEntry === 'object') ? qcEntry : {};
                var paramId    = String(qcEntry.parameter_id || '');
                var paramVal   = String(qcEntry.value        || '');
                var dedAmt     = parseFloat(qcEntry.deductionamt) || 0;
                var qcDetailId = String(qcEntry.id           || '');
                var hvalue     = String(qcEntry.hvalue       || '');
                if (!paramId) return;
                var qcRowData    = paramIdMap[paramId] || null;
                var paramName    = qcRowData ? (qcRowData.ParameterName || '') : ('Param#' + paramId);
                var calcBy       = qcRowData ? (qcRowData.CalculationBy  || '') : '';
                var minVal       = qcRowData ? (qcRowData.MinValue  || '-') : '-';
                var maxVal       = qcRowData ? (qcRowData.MaxValue  || '-') : '-';
                var baseVal      = qcRowData ? (qcRowData.BaseValue || '-') : '-';
                var deduction    = qcRowData ? findDeduction(paramVal, qcRowData.deduction_matrix) : null;
                var reductionAmt = (deduction !== null && basicRate > 0)
                  ? calcReductionAmount(calcBy, basicRate, deduction) : dedAmt;
                newQcValuesStore[ri][paramId] = {
                  pct: paramVal, paramName, parameter_id: paramId,
                  qcDetailId, hvalue,
                  deduction, calcBy, reductionAmt, basicRate,
                  minVal, maxVal, baseVal
                };
              });
              updateQCBadgesWithParamNames(ri);
            });
            $.each(qcValuesStore, function(ri, vals) {
              if (newQcValuesStore[ri] === undefined) newQcValuesStore[ri] = vals;
            });
            qcValuesStore = newQcValuesStore;
          }
          rebuildDeductionMatrixTable();
        } else {
          toastr.error(res.message || 'Failed to save Stack QC details!');
        }
      },
      error: function() {
        $('#sq_update_btn').prop('disabled', false).html('<i class="fa fa-save"></i> UPDATE STACK DETAILS');
        toastr.error('Something went wrong!');
      }
    });
  });

  // =====================================================================
  // *** COMMON FUNCTION: saveHeadQCToServer ***
  // Both advance_payment_btn and carry_forward_btn share this single function.
  //
  // @param options {
  //   $btn          : jQuery button element
  //   btnResetHtml  : string   — original button HTML to restore after save
  //   successMsg    : string|function — success toastr message (optional, can be a function for dynamic text)
  //   onBeforeSave  : function() — optional callback executed before AJAX (used by carry forward to copy values)
  //                               return false to cancel the AJAX call
  // }
  // =====================================================================
  function saveHeadQCToServer(options) {
    var $btn         = options.$btn;
    var btnResetHtml = options.btnResetHtml;
    // successMsg can be a plain string or a function (for dynamic messages)
    var successMsg   = options.successMsg || 'Head QC saved successfully!';
    var onBeforeSave = options.onBeforeSave || null;

    // Execute onBeforeSave callback if provided (used by carry forward to copy Centre QC values)
    if (typeof onBeforeSave === 'function') {
      var shouldContinue = onBeforeSave();
      if (shouldContinue === false) return; // if callback returns false, abort the save
    }

    var GateINID  = $('#sq_GateINID').val() || '<?= $gatein->GateINID; ?>';
    var csrfToken = $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val();

    var idArr           = [];
    var valueArr        = [];
    var deductionAmtArr = [];

    $('#deduction_matrix_tbody tr.dm-param-row').each(function() {
      var $tr        = $(this);
      var qcDetailID = $tr.data('qc-detail-id') || '';
      var centreQC   = $tr.find('td').eq(5).text().trim();
      var amount     = $tr.find('.dm-amount-cell .amount-value').text().trim();
      var headQCVal  = $tr.find('.dm-head-qc-input').val() || '';

      if (qcDetailID !== '') {
        idArr.push(qcDetailID);
        valueArr.push(headQCVal !== '' ? headQCVal : centreQC);
        deductionAmtArr.push((amount !== '' && amount !== '-') ? amount : '0.00');
      }
    });

    if (!GateINID) { toastr.warning('GateINID not found!'); return; }
    if (idArr.length === 0) { toastr.warning('No QC detail data found.'); return; }

    var currentHeadQcFormMode = $('#head_qc_form_mode').val() || 'add';

    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    var postData = { GateINID: GateINID, form_mode: currentHeadQcFormMode };

    idArr.forEach(function(id)           { if (!postData['id[]'])           postData['id[]']           = []; postData['id[]'].push(id); });
    valueArr.forEach(function(v)         { if (!postData['value[]'])        postData['value[]']        = []; postData['value[]'].push(v); });
    deductionAmtArr.forEach(function(d)  { if (!postData['deductionAmt[]']) postData['deductionAmt[]'] = []; postData['deductionAmt[]'].push(d); });
    postData['<?= $this->security->get_csrf_token_name(); ?>'] = csrfToken;

    $.ajax({
      url     : '<?= admin_url('purchase/Inwards/SaveHeadQCDetails'); ?>',
      type    : 'POST',
      data    : postData,
      dataType: 'json',
      success : function(res) {
        $btn.prop('disabled', false).html(btnResetHtml);
        if (res.success == true) {
          toastr.success(typeof successMsg === 'function' ? successMsg() : successMsg);

          // Set form_mode to 'edit' since data now exists on the server
          $('#head_qc_form_mode').val('edit');

          // Update qcValuesStore and headQCStore with data returned from the server response
          var resRows = [];
          if (Array.isArray(res.data)) resRows = res.data;
          else if (res.data && typeof res.data === 'object') resRows = [res.data];

          resRows.forEach(function(resRow, ri) {
            var $tr = $('#stack_qc_tbody tr[data-row="' + ri + '"]');
            if (!$tr.length) return;
            var itemID    = $tr.find('.sq-item-select').val() || '';
            var item      = itemDataMap[itemID] || {};
            var basicRate = parseFloat(item.BasicRate) || 0;
            var qcRowsList= modalQCData[ri] || [];
            var paramIdMap= {};
            qcRowsList.forEach(function(r) { paramIdMap[String(r.id)] = r; });

            var newQcArr = Array.isArray(resRow.qc) ? resRow.qc : [];
            $tr.find('.qc-display-input').attr('data-qc-json', JSON.stringify(newQcArr));

            newQcArr.forEach(function(qcEntry) {
              var paramId    = String(qcEntry.parameter_id || '');
              var qcDetailId = String(qcEntry.id           || '');
              var hvalue     = String(qcEntry.hvalue       || '');
              if (!paramId) return;

              // Update hvalue and qcDetailId in qcValuesStore for the current parameter
              if (qcValuesStore[ri] && qcValuesStore[ri][paramId]) {
                qcValuesStore[ri][paramId].hvalue     = hvalue;
                qcValuesStore[ri][paramId].qcDetailId = qcDetailId;
              }

              // Rebuild and update headQCStore entry for this parameter
              var storeKey  = ri + '_' + paramId;
              var qcRowData = paramIdMap[paramId] || null;
              var calcBy    = qcRowData ? (qcRowData.CalculationBy || '') : '';
              if (hvalue !== '') {
                var headDedMatrix = (qcRowData && qcRowData.deduction_matrix) ? qcRowData.deduction_matrix : [];
                var headDed       = findDeduction(hvalue, headDedMatrix);
                var headAmt       = 0;
                if (headDed !== null && basicRate > 0) headAmt = calcReductionAmount(calcBy, basicRate, headDed);
                else if (headDed !== null) headAmt = parseFloat(headDed) || 0;
                headQCStore[storeKey] = { value: hvalue, deduction: headDed, amount: headAmt, calcBy: calcBy, basicRate: basicRate };

                // Update the corresponding Head QC input field in the Deduction Matrix table
                var $inputInMatrix = $('#deduction_matrix_tbody tr.dm-param-row[data-qc-detail-id="' + qcDetailId + '"] .dm-head-qc-input');
                if ($inputInMatrix.length) $inputInMatrix.val(hvalue);
              }
            });
          });

          // Rebuild the Deduction Matrix table to reflect updated values
          rebuildDeductionMatrixTable();

        } else {
          toastr.error(res.message || 'Save failed!');
        }
      },
      error : function() {
        $btn.prop('disabled', false).html(btnResetHtml);
        toastr.error('Something went wrong!');
      }
    });
  }

  // =====================================================================
  // ADVANCE PAYMENT BUTTON — calls saveHeadQCToServer to save Head QC data directly
  // =====================================================================
  $(document).on('click', '#advance_payment_btn', function() {
    saveHeadQCToServer({
      $btn        : $(this),
      btnResetHtml: '<i class="fa fa-money"></i> ADVANCE PAYMENT',
      successMsg  : 'Head QC saved successfully!'
      // No onBeforeSave needed — saves current Head QC values directly
    });
  });

  // =====================================================================
  // CARRY FORWARD BUTTON — copies Centre QC values into Head QC inputs, then calls saveHeadQCToServer
  // =====================================================================
  $(document).on('click', '#carry_forward_btn', function() {
    var carriedCount = 0;

    saveHeadQCToServer({
      $btn        : $(this),
      btnResetHtml: '<i class="fa fa-forward"></i> Carry Forward',

      // onBeforeSave: copies all Centre QC values into the Head QC input fields before AJAX
      onBeforeSave: function() {
        $('#deduction_matrix_tbody tr.dm-param-row').each(function() {
          var $tr         = $(this);
          var centreQCVal = $tr.find('td').eq(5).text().trim();
          var $headInput  = $tr.find('.dm-head-qc-input');

          if (centreQCVal !== '' && centreQCVal !== '-' && !isNaN(parseFloat(centreQCVal))) {
            $headInput.val(centreQCVal);

            var storeKey     = $headInput.data('store-key');
            var calcBy       = $headInput.data('calc-by') || '';
            var basicRate    = parseFloat($headInput.data('basic-rate')) || 0;
            var amountCellId = $headInput.data('amount-cell-id');

            // Parse the deduction matrix JSON from the input's data attribute
            var matrixRaw       = $headInput.attr('data-deduction-matrix') || '';
            var deductionMatrix = [];
            if (matrixRaw) {
              try {
                var tempDiv = document.createElement('div');
                tempDiv.innerHTML = matrixRaw;
                deductionMatrix = JSON.parse(tempDiv.textContent || tempDiv.innerText || '[]');
              } catch(e) {
                if (deductionMatrixStore[storeKey]) {
                  deductionMatrix = deductionMatrixStore[storeKey].matrix    || [];
                  calcBy          = deductionMatrixStore[storeKey].calcBy    || calcBy;
                  basicRate       = deductionMatrixStore[storeKey].basicRate || basicRate;
                }
              }
            }
            if (deductionMatrix.length === 0 && deductionMatrixStore[storeKey]) {
              deductionMatrix = deductionMatrixStore[storeKey].matrix    || [];
              calcBy          = deductionMatrixStore[storeKey].calcBy    || calcBy;
              basicRate       = deductionMatrixStore[storeKey].basicRate || basicRate;
            }

            // Calculate deduction and reduction amount based on the carried-forward Centre QC value
            var deduction    = findDeduction(centreQCVal, deductionMatrix);
            var reductionAmt = 0;
            if (deduction !== null && basicRate > 0) {
              reductionAmt = calcReductionAmount(calcBy, basicRate, deduction);
            } else if (deduction !== null) {
              reductionAmt = parseFloat(deduction) || 0;
            }

            // Save the calculated values into headQCStore
            headQCStore[storeKey] = {
              value: centreQCVal, deduction: deduction,
              amount: reductionAmt, calcBy: calcBy, basicRate: basicRate
            };

            // Update the Amount cell in the Deduction Matrix table row
            var $amountCell = $('#' + amountCellId);
            $amountCell.addClass('dm-amount-updated');
            $amountCell.html('<span class="amount-value">' + (reductionAmt > 0 ? reductionAmt.toFixed(4) : '-') + '</span>');

            carriedCount++;
          }
        });

        if (carriedCount === 0) {
          toastr.warning('NO DATA FOUND!');
          return false; // return false to cancel the saveHeadQCToServer AJAX call
        }

        // Recalculate all deduction totals after copying values
        recalcDeductionTotals();


      },

      successMsg: function() { return carriedCount + ' values carry forward and saved!'; }
      // successMsg is a function so carriedCount is read after onBeforeSave runs, giving the correct count
    });
  });

  $(document).on('input change', 'input[name="weight[]"], input[name="bag_qty[]"]', function() {
    rebuildDeductionMatrixTable();
  });
</script>