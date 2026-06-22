(function ($) {
	'use strict';

	var debounceTimer;
	var currentView = localStorage.getItem('arquivoHistoricoView') || 'grid';
	var $results = $('#resultados-documentos');
	var $form = $('#arquivo-historico-search-form');

	function escapeHtml(text) {
		return $('<div>').text(text || '').html();
	}

	function sanitizeHighlightHtml(html) {
		var wrapper = document.createElement('div');
		wrapper.innerHTML = String(html || '');
		wrapper.querySelectorAll('*').forEach(function (node) {
			if (node.tagName !== 'MARK') {
				node.replaceWith(document.createTextNode(node.textContent || ''));
			} else {
				Array.prototype.slice.call(node.attributes).forEach(function (attr) {
					node.removeAttribute(attr.name);
				});
			}
		});
		return wrapper.innerHTML;
	}

	function setView(view) {
		currentView = view === 'list' ? 'list' : 'grid';
		if (!$results.length) {
			return;
		}
		$results.removeClass('document-grid document-list').addClass(currentView === 'list' ? 'document-list' : 'document-grid');
		localStorage.setItem('arquivoHistoricoView', currentView);
	}

	function renderLoading() {
		if (!$results.length) {
			return;
		}
		$results.html('<div class="loading-state"><div class="loading-skeleton"></div><div class="loading-skeleton"></div><div class="loading-skeleton"></div>' + escapeHtml(arquivoHistoricoData.i18n.loading) + '</div>');
	}

	function buildCard(item) {
		var thumb = item.thumb ? '<img loading="lazy" alt="' + escapeHtml(item.titulo) + '" src="' + escapeHtml(item.thumb) + '">' : '';
		var download = item.pdf ? '<a class="button" href="' + escapeHtml(item.pdf) + '" download>Download</a>' : '';
		return '<article class="document-card"><a href="' + escapeHtml(item.link) + '">' + thumb + '</a><div class="document-card-body"><h3><a href="' + escapeHtml(item.link) + '">' + sanitizeHighlightHtml(item.titulo) + '</a></h3><p class="document-meta">' + escapeHtml(item.data || '') + '</p><p>' + sanitizeHighlightHtml(item.resumo) + '</p><p><a class="button" href="' + escapeHtml(item.link) + '">Ver documento</a> ' + download + '</p></div></article>';
	}

	function renderResults(data) {
		if (!$results.length) {
			return;
		}

		if (!data.documentos || !data.documentos.length) {
			$results.html('<div class="empty-state">' + escapeHtml(arquivoHistoricoData.i18n.empty) + '</div>');
			return;
		}

		var html = '';
		data.documentos.forEach(function (item) {
			html += buildCard(item);
		});
		$results.html(html);
		setView(currentView);
	}

	function updateUrl(params) {
		var query = new URLSearchParams(params).toString();
		history.pushState({}, '', window.location.pathname + (query ? '?' + query : ''));
	}

	function runSearch(page) {
		if (!$form.length) {
			return;
		}
		renderLoading();

		var payload = {
			action: 'busca_documentos',
			nonce: arquivoHistoricoData.nonce,
			termo: $('#termo_busca').val() || '',
			categoria: $('#filtro_categoria').val() || '',
			periodo: $('#filtro_periodo').val() || '',
			tema: $('#filtro_tema').val() || '',
			ordem: $('#ordem_documentos').val() || 'date_desc',
			pagina: page || 1
		};

		$.post(arquivoHistoricoData.ajaxurl, payload, function (response) {
			if (response && response.success) {
				renderResults(response.data);
				updateUrl({ s: payload.termo, categoria: payload.categoria, periodo: payload.periodo, tema: payload.tema, pagina: payload.pagina, post_type: 'documento' });
			}
		});
	}

	function runAutocomplete() {
		var termo = $('#termo_busca').val();
		if (!termo || termo.length < 2) {
			$('#autocomplete-list').empty();
			return;
		}
		$.post(arquivoHistoricoData.ajaxurl, {
			action: 'autocomplete_busca',
			nonce: arquivoHistoricoData.nonce,
			termo: termo
		}, function (response) {
			if (!response || !response.success) {
				return;
			}
			var html = '<ul>';
			response.data.forEach(function (item) {
				html += '<li><a href="' + escapeHtml(item.url) + '">' + escapeHtml(item.texto) + '</a></li>';
			});
			html += '</ul>';
			$('#autocomplete-list').html(html);
		});
	}

	$(document).on('submit', '#arquivo-historico-search-form', function (e) {
		e.preventDefault();
		runSearch(1);
	});

	$(document).on('input', '#termo_busca', function () {
		clearTimeout(debounceTimer);
		debounceTimer = setTimeout(function () {
			runAutocomplete();
		}, 300);
	});

	$(document).on('change', '#filtro_categoria, #filtro_periodo, #filtro_tema, #ordem_documentos', function () {
		runSearch(1);
	});

	$(document).on('click', '.js-view-toggle', function () {
		setView($(this).data('view'));
	});

	$(document).on('click', '[data-copy-link]', function () {
		var link = $(this).attr('data-copy-link');
		if (navigator.clipboard && link) {
			navigator.clipboard.writeText(link);
		}
	});

	setView(currentView);
})(jQuery);
