
$(document).ready(function () {
    $('form :input').blur(function () {
        if ($(this).hasClass('myInput1') && $(this).val()) {
            if($(this).next('.myInput2').val()){
                $(this).closest('form').submit();
            }
        }else if ($(this).hasClass('myInput2') && $(this).val()) {
            if($(this).prev('.myInput1').val()){
                $(this).closest('form').submit();
            }
        }
    });
});




