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

    if ($('.jbp-pro-list').size() > 0) {
        $('.jbp-pro-list').find('.expert-avatar').each(function () {
            var img = $(this).find('img').first();
            $(this).css('height',$(this).width());
            if (!img.hasClass('avatar')) {
                img.css({
                    'height': '100%',
                    'width': 'auto'
                })
            }
        })

    }
})
