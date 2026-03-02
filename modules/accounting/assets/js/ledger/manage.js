var fnServerParams;
(function($) {
		"use strict";

		fnServerParams = {
      "from_date": '[name="from_date"]',
      "to_date": '[name="to_date"]',
      "client": '[name="client"]',
    };
    init_ladger_entry_table();
    $('input[name="from_date"]').on('change', function() {
      init_ladger_entry_table();
    });
    
    $('select[name="client"]').on('change', function() {
      init_ladger_entry_table();
    });
    
    $('input[name="to_date"]').on('change', function() {
      init_ladger_entry_table();
    });

	$("input[data-type='currency']").on({
      keyup: function() {
        formatCurrency($(this));
      },
      blur: function() {
        formatCurrency($(this), "blur");
      }
    });
})(jQuery);

function init_ladger_entry_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-ladger-entry')) {
    $('.table-ladger-entry').DataTable().destroy();
  }
  initDataTable('.table-ladger-entry', admin_url + 'accounting/ledger_entry_table', [0], [0], fnServerParams, [0, 'ASC']);
}


// journal entry bulk actions action
function bulk_action(event) {
  "use strict";
    if (confirm_delete()) {
        var ids = [],
            data = {};
            data.mass_delete = $('#mass_delete').prop('checked');

        var rows = $($('#journal_entry_bulk_actions').attr('data-table')).find('tbody tr');

        $.each(rows, function() {
            var checkbox = $($(this).find('td').eq(0)).find('input');
            if (checkbox.prop('checked') === true) {
                ids.push(checkbox.val());
            }
        });
        data.ids = ids;
        $(event).addClass('disabled');
        setTimeout(function() {
            $.post(admin_url + 'accounting/journal_entry_bulk_action', data).done(function() {
                window.location.reload();
            });
        }, 200);
    }
}