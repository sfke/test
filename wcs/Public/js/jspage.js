$(document).ready(function(){
    $('.page_break .pagesize a:first').addClass('on');
    $('.page_break .pagesize a').click(function(){
        $(".page_break div[class^='page_']").hide();
        if ($(this).hasClass('on')) {
            $('.page_break .page_' + $(this).text()).show();            
        } else {
            $('.page_break .pagesize a').removeClass('on');
            $(this).addClass('on');
            $('.page_break .page_' + $(this).text()).fadeIn(1000);
        }
    });
});