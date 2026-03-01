<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<style>
  table {
    border-collapse: collapse;
    width: 100%;
  }
  th, td {
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
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-10">
        <div class="panel_s">
          <div class="panel-body">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb custombreadcrumb" style="background-color: #fff !important; margin-bottom: 0px !important; ">
                <li class="breadcrumb-item">
                  <a href="<?= admin_url();?>">
                    <b><i class="fa fa-home fa-fw fa-lg"></i></b>
                  </a>
                </li>
                <li class="breadcrumb-item active text-capitalize">
                  <b>Purchase</b>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                  <b>Inward</b>
                </li>
              </ol>
            </nav>
            <hr class="hr_style" />
            <br />
            <div class="row">
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Booking Details:</h4>
                <hr class="hr_style" />
                <!-- <pre>
                  <?= print_r($gatein);?>
                  <?= print_r($inward);?>
                  <?= print_r($order);?>
                </pre> -->
              </div>
              <div class="col-md-12 mbot5">
                <table>
                  <tbody>
                    <tr>
                      <td><b>Account ID : </b></td>
                      <td><?= $inward['AccountID'] ?? '-';?></td>
                      <td><b>Party Name : </b></td>
                      <td><?= $inward['company'] ?? '-';?></td>
                    </tr>
                    <tr>
                      <td><b>Order ID : </b></td>
                      <td><b><?= $inward['OrderID'] ?? '-';?></b></td>
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
                      <td>
                        <a href="" target="_blank" >View ASN</a>
                      </td>
                      <td><b>Gate In Pass : </b></td>
                      <td>
                        <a href="<?= admin_url('purchase/Vehiclein/GateinPassPrint/'.$gatein->GateINID);?>" target="_blank" >View Gate In Pass</a>
                      </td>
                    </tr>
                    <tr>
                      <td><b>ASN Quantity(MT): </b></td>
                      <td>-</td>
                      <td><b>ASN Quantity(Bag): </b></td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td><b>Gate In By : </b></td>
                      <td><?= $gatein->UserID ?? '-';?></td>
                      <td><b>Gate In Date : </b></td>
                      <td><?= date('d/m/Y', strtotime($gatein->TransDate ?? '0000-00-00'));?></td>
                    </tr>
                    <tr>
                      <td><b>Trade Rate (MT) : </b></td>
                      <td>-</td>
                      <td><b>Vehicle No. : </b></td>
                      <td><?= $gatein->VehicleNo ?? '-';?></td>
                    </tr>
                    <tr>
                      <td><b>Center Name : </b></td>
                      <td><?= $gatein->LocationName ?? '-';?></td>
                      <td><b>Status : </b></td>
                      <td>-</td>
                    </tr>
                    <tr>
                      <td><b>Party Invoice : </b></td>
                      <td>
                        <a href="" target="_blank" title="View Party Invoice" >Click to View Party Invoice</a>
                      </td>
                      <td></td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-3 mbot5">
                <div class="form-group" app-field-wrapper="field_officer">
                  <label for="field_officer" class="control-label">
                    <small class="req text-danger">* </small> 
                    Select Field Officer
                  </label>
                  <select name="field_officer" id="field_officer" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($vendor)) :
                        foreach ($vendor as $value) :
                          // echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                </div>
              </div>
              <div class="col-md-3 mbot5" style="padding-top: 12px;">
                <button type="button" class="btn btn-success mtop10">ADD FIELD OFFICER</button>
              </div>
              <div class="col-md-3 mbot5">
                <div class="form-group" app-field-wrapper="send_vehicle_to">
                  <label for="send_vehicle_to" class="control-label">
                    <small class="req text-danger">* </small> 
                    Send Vehicle To
                  </label>
                  <select name="send_vehicle_to" id="send_vehicle_to" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($vendor)) :
                        foreach ($vendor as $value) :
                          // echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                </div>
              </div>
              <div class="col-md-3 mbot5" style="padding-top: 12px;">
                <button type="button" class="btn btn-success mtop10">UPDATE GODOWN</button>
              </div>
              <div class="clearfix"></div>
              <div class="col-md-3 mbot5">
                <div class="form-group" app-field-wrapper="rejection_reason">
                  <label for="rejection_reason" class="control-label">
                    <small class="req text-danger">* </small> 
                    Rejection Reason
                  </label>
                  <textarea name="rejection_reason" id="rejection_reason" class="form-control"></textarea>
                </div>
              </div>
              <div class="col-md-3 mbot5" style="padding-top: 12px;">
                <button type="button" class="btn btn-danger mtop10">REJECT INWARD</button>
              </div>
              <div class="col-md-3 mbot5">
                <div class="form-group" app-field-wrapper="arrival_date_time">
                  <label for="arrival_date_time" class="control-label">
                    <small class="req text-danger">* </small> 
                    Arrival Date Time
                  </label>
                  <input type="datetime-local" name="arrival_date_time" id="arrival_date_time" class="form-control">
                </div>
              </div>
              <div class="col-md-3 mbot5" style="padding-top: 12px;">
                <button type="button" class="btn btn-success mtop10">VEHICAL ARRIVAL AT</button>
              </div>
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Add Village Details:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-3 mbot5">
                <div class="form-group" app-field-wrapper="pincode">
                  <label for="pincode" class="control-label">Pincode</label>
                  <input type="tel" name="pincode" id="pincode" class="form-control" pattern="[0-9]{6}" maxlength="6" minlength="6">
                </div>
              </div>
              <div class="col-md-3 mbot5">
                <div class="form-group" app-field-wrapper="village">
                  <label for="village" class="control-label">Select Village</label>
                  <select name="village" id="village" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($vendor)) :
                        foreach ($vendor as $value) :
                          // echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="col-md-3 mbot5">
                <div class="form-group" app-field-wrapper="state">
                  <label for="state" class="control-label">State</label>
                  <select name="state" id="state" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($vendor)) :
                        foreach ($vendor as $value) :
                          // echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                </div>
              </div>
              <div class="col-md-3 mbot5">
                <div class="form-group" app-field-wrapper="district">
                  <label for="district" class="control-label">District</label>
                  <select name="district" id="district" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($vendor)) :
                        foreach ($vendor as $value) :
                          // echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                </div>
              </div>
              <div class="col-md-3 mbot5">
                <div class="form-group" app-field-wrapper="taluka">
                  <label for="taluka" class="control-label">Taluka</label>
                  <select name="taluka" id="taluka" class="form-control selectpicker" data-live-search="true">
                      <option value="" selected disabled>None selected</option>
                      <?php
                      if (!empty($vendor)) :
                        foreach ($vendor as $value) :
                          // echo '<option value="' . $value['AccountID'] . '">' . $value['company'] . ' ('.$value['AccountID'].')</option>';
                        endforeach;
                      endif;
                      ?>
                    </select>
                </div>
              </div>
              <div class="col-md-3 mbot5" style="padding-top: 12px;">
                <button type="button" class="btn btn-success mtop10">SUBMIT</button>
              </div>
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Peripheral QC Details:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-12 mbot5">
                <table>
                  <tbody>
                    <tr>
                      <th>Moisture</th>
                      <th>Damaged</th>
                      <th>Foreign Material</th>
                      <th>Small Seeds</th>
                      <th>UserID</th>
                      <th>Date Time</th>
                      <th>Action</th>
                    </tr>
                    <tr>
                      <td>
                        <input type="text" name="moisture" id="moisture" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="damaged" id="damaged" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="foreign_material" id="foreign_material" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="small_seeds" id="small_seeds" class="form-control">
                      </td>
                      <td></td>
                      <td><?= date('Y-m-d H:i:s'); ?></td>
                      <td style="width: 20px;">
                        <button type="button" class="btn btn-success"><i class="fa fa-save"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Gross Weight Details:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-12 mbot5">
                <form action="" method="post" id="gross_weight_form">
                  <input type="hidden" name="type" value="GrossWeight">
                  <table>
                    <tbody>
                      <tr>
                        <th>Total Weight(MT)</th>
                        <th>Top Image</th>
                        <th>Front Image</th>
                        <th>Side Image</th>
                        <th>Loaded By</th>
                        <th>Loaded Date Time</th>
                        <th>Action</th>
                      </tr>
                      <tr>
                        <td>
                          <input type="text" name="total_weight" id="total_weight" class="form-control" required>
                        </td>
                        <td>
                          <input type="file" name="top_image" id="top_image" accept="image/*" required style="display: none;">
                          <label for="top_image">Upload</label>
                        </td>
                        <td>
                          <input type="file" name="front_image" id="front_image" accept="image/*" required style="display: none;">
                          <label for="front_image">Upload</label>
                        </td>
                        <td>
                          <input type="file" name="side_image" id="side_image" accept="image/*" required style="display: none;">
                          <label for="side_image">Upload</label>
                        </td>
                        <td></td>
                        <td></td>
                        <td style="width: 20px;">
                          <button type="submit" class="btn btn-success"><i class="fa fa-save"></i></button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </form>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12 mbot5">
                    <h4 class="bold p_style">Cleaning Details:</h4>
                    <hr class="hr_style" />
                  </div>
                  <div class="col-md-12 mbot5">
                    <table>
                      <tbody>
                        <tr>
                          <th>FM (Kg)</th>
                          <th>Cleaning By</th>
                          <th>Cleaning Date Time</th>
                          <th>Action</th>
                        </tr>
                        <tr>
                          <td>
                            <input type="text" name="fm_kg" id="fm_kg" class="form-control">
                          </td>
                          <td></td>
                          <td><?= date('Y-m-d H:i:s'); ?></td>
                          <td style="width: 20px;">
                            <button type="button" class="btn btn-success"><i class="fa fa-save"></i></button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12 mbot5">
                    <h4 class="bold p_style">Bag Weight Details:</h4>
                    <hr class="hr_style" />
                  </div>
                  <div class="col-md-12 mbot5">
                    <table>
                      <tbody>
                        <tr>
                          <th>Empty Bag Weight (Kg)</th>
                          <th>Added By</th>
                          <th>Added Date Time</th>
                          <th>Action</th>
                        </tr>
                        <tr>
                          <td>
                            <input type="text" name="empty_bag_weight" id="empty_bag_weight" class="form-control">
                          </td>
                          <td></td>
                          <td><?= date('Y-m-d H:i:s'); ?></td>
                          <td style="width: 20px;">
                            <button type="button" class="btn btn-success"><i class="fa fa-save"></i></button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Tare Weight Details:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-12 mbot5">
                <table>
                  <tbody>
                    <tr>
                      <th>Tare Weight(MT)</th>
                      <th>Top Image</th>
                      <th>Front Image</th>
                      <th>Side Image</th>
                      <th>Uploaded By</th>
                      <th>Uploaded Date Time</th>
                      <th>Action</th>
                    </tr>
                    <tr>
                      <td>
                        <input type="text" name="tare_weight" id="tare_weight" class="form-control">
                      </td>
                      <td>
                        <a href="">View Image</a>
                      </td>
                      <td>
                        <a href="">View Image</a>
                      </td>
                      <td>
                        <a href="">View Image</a>
                      </td>
                      <td></td>
                      <td><?= date('Y-m-d H:i:s'); ?></td>
                      <td style="width: 20px;">
                        <button type="button" class="btn btn-success"><i class="fa fa-save"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Center QC & Stack Details:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-12 mbot5">
                <table class="mbot5">
                  <tbody>
                    <tr>
                      <th>Moisture</th>
                      <th>Damaged</th>
                      <th>Foreign Material</th>
                      <th>Small Seeds</th>
                      <th>QC Approval</th>
                      <th>Chamber</th>
                      <th>Stack</th>
                      <th>Lot</th>
                      <th>Weight(MT)</th>
                      <th>Bag Qty</th>
                      <th>QC Status</th>
                      <th>Action</th>
                    </tr>
                    <tr>
                      <td>
                        <input type="text" name="moisture" id="moisture" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="damaged" id="damaged" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="foreign_material" id="foreign_material" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="small_seeds" id="small_seeds" class="form-control">
                      </td>
                      <td>
                        <select name="qc_approval" id="qc_approval" class="form-control selectpicker">
                          <option value="" selected disabled>None</option>
                          <option value="approved">Approved</option>
                          <option value="rejected">Rejected</option>
                        </select>
                      </td>
                      <td>
                        <input type="text" name="chamber" id="chamber" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="stack" id="stack" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="lot" id="lot" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="weight_mt" id="weight_mt" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="bag_qty" id="bag_qty" class="form-control">
                      </td>
                      <td>
                        <select name="qc_status" id="qc_status" class="form-control selectpicker">
                          <option value="" selected disabled>None</option>
                          <option value="passed">Passed</option>
                          <option value="failed">Failed</option>
                        </select>
                      </td>
                      <td style="width: 20px;">
                        <button type="button" class="btn btn-success"><i class="fa fa-plus"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <input type="text" name="moisture" id="moisture" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="damaged" id="damaged" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="foreign_material" id="foreign_material" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="small_seeds" id="small_seeds" class="form-control">
                      </td>
                      <td>
                        <select name="qc_approval" id="qc_approval" class="form-control selectpicker">
                          <option value="" selected disabled>None</option>
                          <option value="approved">Approved</option>
                          <option value="rejected">Rejected</option>
                        </select>
                      </td>
                      <td>
                        <input type="text" name="chamber" id="chamber" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="stack" id="stack" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="lot" id="lot" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="weight_mt" id="weight_mt" class="form-control">
                      </td>
                      <td>
                        <input type="text" name="bag_qty" id="bag_qty" class="form-control">
                      </td>
                      <td>
                        <select name="qc_status" id="qc_status" class="form-control selectpicker">
                          <option value="" selected disabled>None</option>
                          <option value="passed">Passed</option>
                          <option value="failed">Failed</option>
                        </select>
                      </td>
                      <td style="width: 20px;">
                        <button type="button" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                
                <button type="button" class="btn btn-success">UPDATE STACK DETAILS</button>
              </div>
              <div class="col-md-12 mbot5">
                <h4 class="bold p_style">Deduction Matrix:</h4>
                <hr class="hr_style" />
              </div>
              <div class="col-md-6 mbot5">
                <table class="mbot5">
                  <thead>
                    <tr>
                      <th>Parameter</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><b>ASN Weight(MT)</b></td>
                      <td class="text-right">3.610</td>
                    </tr>
                    <tr>
                      <td><b>Purchase Amount</b></td>
                      <td class="text-right">187720.00</td>
                    </tr> 
                    <tr>
                      <td><b>Actual Weight (MT)</b></td>
                      <td class="text-right">3.610</td>
                    </tr>
                    <tr>
                      <td><b>Actual Inward Weight (MT)</b></td>
                      <td class="text-right">3.610</td>
                    </tr>
                    <tr>
                      <td>Moisture</td>
                      <td class="text-right">938.6</td>
                    </tr>
                    <tr>
                      <td>Damaged</td>
                      <td class="text-right">0</td>
                    </tr>
                    <tr>
                      <td>Foreign Material</td>
                      <td class="text-right">0</td>
                    </tr>
                    <tr>
                      <td>Small Seeds</td>
                      <td class="text-right">0</td>
                    </tr>
                    <tr>
                      <td>Bag Weight</td>
                      <td class="text-right">0.00</td>
                    </tr>
                    <tr>
                      <td><b>Total Deduction</b></td>
                      <td class="text-right">938.60</td>
                    </tr>
                    <tr>
                      <td><b>Final Rate/MT</b></td>
                      <td class="text-right">51740.000</td>
                    </tr>
                    <tr>
                      <td><b>Net Amount</b></td>
                      <td class="text-right">186781.40</td>
                    </tr>
                  </tbody>
                </table>
                
                <button type="button" class="btn btn-success">ADVANCE PAYMENT</button>
              </div>
              <div class="clearfix"></div>
              <div class="col-md-6 mbot5">
                <div class="row">
                  <div class="col-md-12 mbot5">
                    <h4 class="bold p_style">Gate Out Pass:</h4>
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
                          <td></td>
                          <td><?= date('Y-m-d H:i:s'); ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mbot5">
                <div class="row">
                  <div class="col-md-12 mbot5">
                    <h4 class="bold p_style">Exit Marked:</h4>
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
                          <td></td>
                          <td><?= date('Y-m-d H:i:s'); ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
