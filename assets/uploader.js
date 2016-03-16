 (function( $ ) {
    'use strict';
    
    function renderMediaUploader() {
     
        var file_frame, image_data;
     
        if ( undefined !== file_frame ) {
     
            file_frame.open();
            return;
     
        }
        
        file_frame = wp.media.frames.file_frame = wp.media({
            frame:    'post',
            state:    'insert',
            multiple: false
        });
        
        file_frame.on( 'insert', function() {

            var json = file_frame.state().get( 'selection' ).first().toJSON();

            if ( 0 > $.trim( json.url.length ) ) {
                return;
            }

            $( '#je_ftr_img_container' )
                .children( 'img' )
                    .attr( 'src', json.url )
                    .attr( 'alt', json.caption )
                    .attr( 'title', json.title )
                                .show()
                .parent()
                .removeClass( 'hidden' );
            
            $( '#job_img' ).val( json.id );
            $( '#je_ftr_img_rmv' ).removeClass( 'hidden' );
        });
     
        file_frame.open();
    }
 
    $(function() {
        $( '#je_ftr_img' ).on( 'click', function( evt ) {
 
            evt.preventDefault();
            renderMediaUploader();
            
        });
        
        $( '#je_ftr_img_rmv' ).click( function( e ) {
            e.preventDefault();
            
            $( this ).addClass( 'hidden' );
            $( '#je_ftr_img_container' ).addClass( 'hidden' ).find( 'img' ).attr( 'src', '' );
            $( '#job_img' ).val( '' );
            
            return false;
        } );
    });
 
})( jQuery );