(function ($) {
	'use strict';

	var maxSize = 50 * 1024 * 1024;

	function validateFile(file) {
		var ext = (file.name.split('.').pop() || '').toLowerCase();
		if (ext !== 'pdf') {
			return 'Arquivo inválido: apenas PDF é permitido.';
		}
		if (file.size > maxSize) {
			return 'Arquivo excede 50MB.';
		}
		return '';
	}

	$(document).on('change', '#arquivo_historico_bulk_files', function () {
		var files = this.files || [];
		var $status = $('#arquivo_historico_bulk_status');
		$status.empty();

		Array.prototype.forEach.call(files, function (file) {
			var error = validateFile(file);
			if (error) {
				$status.append('<p>' + error + ' (' + file.name + ')</p>');
				return;
			}
			$status.append('<p>Pronto para upload: ' + file.name + '</p><progress max="100" value="0"></progress>');
		});
	});

	$(document).on('click', '#arquivo_historico_open_media', function (e) {
		e.preventDefault();
		if (!wp || !wp.media) {
			return;
		}
		var frame = wp.media({
			title: 'Selecionar PDFs',
			multiple: true,
			library: { type: 'application/pdf' }
		});
		frame.on('select', function () {
			var selection = frame.state().get('selection');
			if (!selection || !selection.length) {
				return;
			}
			var first = selection.first();
			if (first && first.get('url')) {
				$('#_arquivo_pdf').val(first.get('url')).trigger('change');
			}
		});
		frame.open();
	});
})(jQuery);
