jQuery(document).ready(function($){
    $('.toggle').click(function(e){
        e.preventDefault();
        var h4 = $(this);
        var box_id = h4.attr('data');
        $('.box-inner').each(function(index) {
            if ($(this).attr("id") == box_id) {
                $(this).slideToggle();
                if(h4.hasClass('closed')){
                    h4.removeClass('closed');
                    h4.addClass('opened');
                }
                else{
                    h4.removeClass('opened');
                    h4.addClass('closed');
                }
            }
        });
    });

    $('.check_all').change(function() {
        var checkboxes = $(this).closest('form').find(':checkbox');
        if($(this).is(':checked')) {
            checkboxes.prop('checked', true);
        } else {
            checkboxes.prop('checked', false);
        }
    });
    
    $('.ilgen-watch-input').change(function(e){
        e.preventDefault();
        $(this).closest('tr').find(':checkbox').prop('checked', true);
        $('select[name=bulk_action] option[value=update]').attr("selected", "selected");
        $('.ilgen-watch-notification').css('display','inline-block');
    });
});