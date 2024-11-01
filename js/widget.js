jQuery(document).ready( function($) {
    function ciw_custom_media_upload(button_class) {
        var _custom_media = true,
        _orig_send_attachment = wp.media.editor.send.attachment;
        jQuery('body').on('click', button_class, function(e) {
            var button_id ='#'+jQuery(this).attr('id');
            var self = jQuery(button_id);
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = jQuery(button_id);
            var id = button.attr('id').replace('_button', '');
            _custom_media = true;
            wp.media.editor.send.attachment = function(props, attachment){
                if ( _custom_media  ) {
                    jQuery('.content').show();
                    jQuery('.custom_media_id').val(attachment.id);
                    jQuery('.custom_media_url').val(attachment.url).trigger("change");
                    jQuery('.custom_media_image').attr('src',attachment.url).css('display','block');
                    jQuery('.title').val(attachment.title);
                    jQuery('.alt_text').val(attachment.alt);
                    jQuery('.caption').val(attachment.caption);
                    jQuery('.description').val(attachment.description);
                } else {
                    return _orig_send_attachment.apply( button_id, [props, attachment] );
                }
            }
            wp.media.editor.open(button);
                return false;
        });
    }
    ciw_custom_media_upload('.custom_media_button.button');
    
    jQuery("body").on("change", ".image_size", function(){
        jQuery(this).find("option:selected").each(function(){
            if(jQuery(this).val() == 'custom'){
                jQuery(".custom_size").slideDown();
            } else{
                jQuery(".custom_size").slideUp();
            }
        });
    });
});