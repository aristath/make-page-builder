(function(tinymce, $) {
	if ( 'undefined' !== typeof window.make_pbFormatBuilder ) {
		tinymce.PluginManager.add('make_pb_format_builder', function (editor, url) {
			editor.addCommand('Make_Format_Builder', function () {
				window.make_pbFormatBuilder.open(editor);
			});

			editor.addButton('make_pb_format_builder', {
				icon   : 'make_pb-format-builder',
				tooltip: 'Format Builder',
				cmd    : 'Make_Format_Builder'
			});

			editor.on('init', function () {
				$.each(make_pbFormatBuilder.definitions, function (name, defs) {
					editor.formatter.register(name, defs);
				});
			});
		});
	}
})(tinymce, jQuery);