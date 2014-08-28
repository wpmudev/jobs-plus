jQuery(document).ready(function ($) {
    //auto set icon size
    if ($('.hn-widget').size() > 0) {
        $('.hn-widget').find('.hn-widget-body').each(function () {
            var pwidth = $(this).height();
            pwidth = parseInt(pwidth);
            var ewidth = (pwidth * 70) / 100;
            $(this).find('i').css({
                'font-size': ewidth + 'px',
                position: 'relative',
                top: '50%',
                'margin-top': '-' + ewidth / 2 + 'px'
            })
        });
    }
    if ($('.jbp_pro_except').size() > 0) {
       // $('.jbp_pro_except').find('img').addClass('img-circle');
    }
})