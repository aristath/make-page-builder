( function( tinymce ) {
	if ( 'undefined' !== typeof window.make_pbDynamicStylesheet ) {
		tinymce.PluginManager.add('make_pb_dynamic_stylesheet', function (editor, url) {
			if ('undefined' !== typeof make_pbDynamicStylesheetVars && make_pbDynamicStylesheetVars.tinymce) {
				editor.on('init', function () {
					make_pbDynamicStylesheet.tinymceInit(editor);
				});

				editor.addCommand('Make_Reset_Dynamic_Stylesheet', function () {
					make_pbDynamicStylesheet.resetStylesheet();
				});
			}
		});
	}
} )( tinymce );