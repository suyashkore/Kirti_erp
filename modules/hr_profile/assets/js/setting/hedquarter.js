    
    
    function new_headquarter(){
        'use strict';
        $('#additional_workplace').html('');

        $('#new_headquarter input[name="name"]').val('');
        $('#new_headquarter textarea[name="workplace_address"]').val('');
        $('#new_headquarter input[name="latitude"]').val('');
        $('#new_headquarter input[name="longitude"]').val('');

        $('#new_headquarter').modal('show');
        $('.edit-title').addClass('hide');
        $('.add-title').removeClass('hide');
    }


    function edit_workplace(invoker,id){
        'use strict';

        $('#additional_workplace').html('');
        $('#additional_workplace').append(hidden_input('id',id));

        $('#workplace input[name="name"]').val($(invoker).data('name'));
        $('#workplace textarea[name="workplace_address"]').val($(invoker).data('workplace_address'));
        $('#workplace input[name="latitude"]').val($(invoker).data('latitude'));
        $('#workplace input[name="longitude"]').val($(invoker).data('longitude'));

        $('#workplace').modal('show');
        $('.add-title').addClass('hide');
        $('.edit-title').removeClass('hide');
    }