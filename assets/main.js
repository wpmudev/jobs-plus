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
jQuery.fn.serializeAssoc = function() {
    var data = {};
    jQuery.each( this.serializeArray(), function( key, obj ) {
        var a = obj.name.match(/(.*?)\[(.*?)\]/);
        if(a !== null)
        {
            var subName = a[1];
            var subKey = a[2];
            if( !data[subName] ) data[subName] = [ ];
            if( data[subName][subKey] ) {
                if( jQuery.isArray( data[subName][subKey] ) ) {
                    data[subName][subKey].push( obj.value );
                } else {
                    data[subName][subKey] = [ ];
                    data[subName][subKey].push( obj.value );
                }
            } else {
                data[subName][subKey] = obj.value;
            }
        } else {
            if( data[obj.name] ) {
                if( jQuery.isArray( data[obj.name] ) ) {
                    data[obj.name].push( obj.value );
                } else {
                    data[obj.name] = [ ];
                    data[obj.name].push( obj.value );
                }
            } else {
                data[obj.name] = obj.value;
            }
        }
    });
    return data;
};