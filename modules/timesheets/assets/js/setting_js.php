<script>
  <?php if($group == 'manage_dayoff'){ ?>
    (function(){
      "use strict";
      $('#time_start_work').datetimepicker({
        datepicker: false,
        format: 'H:i'
      });
      $('#time_end_work').datetimepicker({
        datepicker: false,
        format: 'H:i'
      });
      $('#start_lunch_break_time').datetimepicker({
        datepicker: false,
        format: 'H:i'
      });
      $('#end_lunch_break_time').datetimepicker({
        datepicker: false,
        format: 'H:i'
      });
      $('#late_latency_allowed').datetimepicker({
        datepicker: false,
        format: 'H:i'
      });

      $('.date-picker').datepicker( {
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'mm/yy',
        onClose: function(dateText, inst) { 
          $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
        }
      });
    })(jQuery);

    function new_leave(){
      "use strict";
      $('input[name="id"]').val();
      $('#leave_modal').modal('show');
      $('.edit-title').addClass('hide');
      $('.add-title').removeClass('hide');
      $('#leave_modal-form input[name="break_date"]').val('');
      $('#leave_modal-form textarea').val('');
      $('#leave_modal-form select').val('').change();
      $('input[name="repeat_by_year"]').prop('checked', false);      
      appValidateForm($('#leave_modal-form'),{leave_reason:'required',leave_type:'required'});
    }

    function edit_day_off(invoker,id){
      "use strict";
      $('#leave_modal').modal('show');    
      $('.edit-title').removeClass('hide');
      $('.add-title').addClass('hide');
      $('input[name="id"]').val(id);
      $('textarea[name="leave_reason"]').val($(invoker).data('off_reason'));
      $('select[name="leave_type"]').val($(invoker).data('off_type'));
      $('select[name="leave_type"]').change();
      $('select[name="timekeeping"]').val($(invoker).data('timekeeping'));
      $('select[name="timekeeping"]').change();
      var list_id = [];
      var departmentid = $(invoker).data('department').toString();
      if(departmentid.search(',')){
        var list_dpm = departmentid.split(",");
        jQuery.each(list_dpm,function(key, value){
          list_id.push(value);
        });
      }
      else{
       list_id.push(departmentid);
     }
     $('select[name="department[]"]').val(list_id).change();
     $('select[name="position[]"]').val($(invoker).data('position')).change();
     $('input[name="break_date"]').val($(invoker).data('break_date'));
     if($(invoker).data('repeat_by_year') == 1){
      $('input[name="repeat_by_year"]').prop('checked', true);
    }
    else{
      $('input[name="repeat_by_year"]').prop('checked', false);      
    }
    appValidateForm($('#leave_modal_update-form'),{leave_reason:'required',leave_type:'required'});
  }
<?php } ?>

<?php if($group == 'manage_leave'){ ?>

  var data_array = <?php echo html_entity_decode($leave_of_the_year); ?>;
  var hotElement = document.querySelector('#example'), hot;
  (function(){
    "use strict";
    hot = new Handsontable(hotElement, hanson_table(data_array));
  })(jQuery);
  
  function hanson_table(obj){
    "use strict";
    var hotElementContainer = hotElement.parentNode;
    return {
      data: obj,
      columns: [
      {
        data: 'staffid',
        type: 'text',
        readOnly: true
      },
      {
        data: 'staff',
        type: 'text',
        readOnly: true
      },
      {
        data: 'department',
        type: 'text',
        readOnly: true
      },
      {
        data: 'role',
        type: 'text',
        readOnly: true      
      },
      {
        data: 'maximum_leave_of_the_year',
        type: 'numeric',
      }        
      ],
      contextMenu: true,
      manualRowMove: true,
      manualColumnMove: true,
      stretchH: 'all',
      autoWrapRow: true,
      rowHeights: 30,
      defaultRowHeight: 100,
      headerTooltips: true,
      maxRows: 22,
      minHeight:'100%',
      maxHeight:'500px',

      width: '100%',
      height: 330,

      rowHeaders: true,

      autoColumnSize: {
        samplingRatio: 23
      },

      filters: true,
      manualRowResize: true,
      manualColumnResize: true,
      allowInsertRow: true,
      allowRemoveRow: true,
      columnHeaderHeight: 40,

      licenseKey: 'non-commercial-and-evaluation',
      stretchH: 'all',
      width: '100%',
      autoWrapRow: true,
      rowHeights: 30,
      columnHeaderHeight: 40,
      minRows: 10,
      maxRows: 100,
      rowHeaders: true,
      colWidths: [50,200,200,200,200],
      colHeaders: [
      '<?php echo _l('staffid'); ?>',
      '<?php echo _l('staff'); ?>',
      '<?php echo _l('department'); ?>',
      '<?php echo _l('role'); ?>',
      '<?php echo _l('maximum_leave_of_the_year'); ?>',
      ],
      columnSorting: {
        indicator: true
      },
      autoColumnSize: {
        samplingRatio: 23
      },
      dropdownMenu: true,
      mergeCells: true,
      contextMenu: true,
      manualRowMove: true,
      manualColumnMove: true,
      multiColumnSorting: {
        indicator: true
      },
      filters: true,
      manualRowResize: true,
      manualColumnResize: true
    };
  }

  function get_data_hanson(){
    "use strict";
    $('input[name="leave_of_the_year_data"]').val(JSON.stringify(hot.getData()));   
  }

  function filter_hanson(){
    "use strict";
    var staffid = $('select[name="leave_filter_staff[]"]').val();
    var departmentid = $('select[name="leave_filter_department[]"]').val();
    var roleid = $('select[name="leave_filter_roles[]"]').val();
    var data = {};
    data.staffid = staffid;
    data.departmentid = departmentid;
    data.roleid = roleid;
    $.post(admin_url+'timesheets/get_leave_setting',data).done(function(response){
      response = JSON.parse(response);
      hot = new Handsontable(hotElement, hanson_table(response.data));
    });
  }
<?php } ?>
</script>