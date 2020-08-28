
$(function() {
    
    $(document).on('click', '#open-oska-form', function(event) {
        $('#oska-widget').load(STUDIP.URLHelper.getURL('dispatch.php/start') + ' #oska-widget>*', 
            {'show_form': 'true'}, 
            function() {
                oska_prefs();
                toggle_teacher_type();
            }
        );
    });
    
});

function oska_prefs() {
    $('.oska-pref-list').sortable({
        item: '> .oska-pref-item',
        tolerance: 'pointer',
        connectWith: '.oska-pref-list',
        update: function(event, ui) {
            if (ui.sender) {
                ui.item.find('input').val($(this).data('group'));
            }
        },
        over: function(event, ui) {
            $(this).addClass('hover');
        },
        out: function(event, ui) {
            $(this).removeClass('hover');
        },
        receive: function(event, ui) {
            var sortable = $(this);
            var container = sortable.closest('fieldset').find('.oska-pref-container');

            // default answer container can have more items
            if (sortable.children().length > 1 && !sortable.is(container)) {
                sortable.find('.pref-list-item').each(function(i) {
                    if (!ui.item.is(this)) {
                        $(this).find('input').val(-1);
                        $(this).detach().appendTo(container)
                               .css('opacity', 0).animate({opacity: 1});
                    }
                });
            }
        },
    });
}

function toggle_teacher_type() {
    $(document).on('change', 'input:radio[name="teacher"]', function(event) {
        if ($(this).val() == 1) {
        console.log("Test");
            $('input:radio[name="teacher_type"]').each(function(i) {
                $(this).prop('disabled', false);  
            });
        }
        if ($(this).val() != 1) {
            $('input:radio[name="teacher_type"]').each(function(i) {
                $(this).prop('disabled', true);  
            });
        }
    });
}