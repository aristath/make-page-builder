( function( tinymce ) {
	if ( 'undefined' !== typeof window.make_pbIconPicker ) {
		tinymce.PluginManager.add('make_pb_icon_picker', function (editor, url) {
			editor.addCommand('Make_Icon_Picker', function () {
				window.make_pbIconPicker.open(editor, function (value, unicode) {
					if ('undefined' !== unicode) {
						var icon = ' <span class="make_pb-icon mceNonEditable fa">&#x' + unicode + ';</span> ';
						editor.insertContent(icon);
					}
				});
			});

			editor.addButton('make_pb_icon_picker', {
				icon   : 'make_pb-icon-picker',
				tooltip: 'Insert Icon',
				cmd    : 'Make_Icon_Picker'
			});
		});
	}
} )( tinymce );