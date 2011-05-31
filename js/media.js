function fp_send_to_editor(h, close) {
	close = close || false;
    var ed;

    if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
        ed.focus();
        if ( tinymce.isIE )
            ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);

        if ( h.indexOf('[caption') === 0 ) {
            if ( ed.plugins.wpeditimage )
                h = ed.plugins.wpeditimage._do_shcode(h);
        } else if ( h.indexOf('[gallery') === 0 ) {
            if ( ed.plugins.wpgallery )
                h = ed.plugins.wpgallery._do_gallery(h);
        } else if ( h.indexOf('[embed') === 0 ) {
            if ( ed.plugins.wordpress )
                h = ed.plugins.wordpress._setEmbed(h);
        }

        ed.execCommand('mceInsertContent', false, h);

    } else if ( typeof edInsertContent == 'function' ) {
        edInsertContent(edCanvas, h);
    } else {
        jQuery( edCanvas ).val( jQuery( edCanvas ).val() + h );
    }

    if (close) tb_remove();
}
