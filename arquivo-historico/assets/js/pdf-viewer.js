(function () {
	'use strict';

	var wrap = document.querySelector('.viewer-wrap');
	if (!wrap) {
		return;
	}

	var url = wrap.getAttribute('data-pdf-url');
	var canvas = document.getElementById('pdf-render');
	var context = canvas ? canvas.getContext('2d') : null;
	var loadingEl = document.getElementById('pdf-loading');
	var indicator = document.getElementById('pdf-page-indicator');
	var pdfDoc = null;
	var pageNum = 1;
	var scale = 1.2;

	function setLoading(show) {
		if (!loadingEl) return;
		loadingEl.style.display = show ? 'block' : 'none';
	}

	function renderPage(num) {
		if (!pdfDoc || !canvas || !context) return;
		setLoading(true);
		pdfDoc.getPage(num).then(function (page) {
			var viewport = page.getViewport({ scale: scale });
			canvas.height = viewport.height;
			canvas.width = viewport.width;

			var renderContext = {
				canvasContext: context,
				viewport: viewport
			};

			page.render(renderContext).promise.then(function () {
				setLoading(false);
				if (indicator) {
					indicator.textContent = 'Página ' + pageNum + ' de ' + pdfDoc.numPages;
				}
			});
		});
	}

	function fallbackDownload() {
		setLoading(false);
		if (url) {
			window.location.href = url;
		}
	}

	if (!window.pdfjsLib || !url) {
		fallbackDownload();
		return;
	}

	window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.5.136/pdf.worker.min.mjs';
	window.pdfjsLib.getDocument(url).promise.then(function (pdf) {
		pdfDoc = pdf;
		renderPage(pageNum);
	}).catch(function () {
		fallbackDownload();
	});

	document.getElementById('pdf-prev')?.addEventListener('click', function () {
		if (pageNum <= 1) return;
		pageNum -= 1;
		renderPage(pageNum);
	});

	document.getElementById('pdf-next')?.addEventListener('click', function () {
		if (!pdfDoc || pageNum >= pdfDoc.numPages) return;
		pageNum += 1;
		renderPage(pageNum);
	});

	document.getElementById('pdf-zoom-in')?.addEventListener('click', function () {
		scale += 0.15;
		renderPage(pageNum);
	});

	document.getElementById('pdf-zoom-out')?.addEventListener('click', function () {
		scale = Math.max(0.5, scale - 0.15);
		renderPage(pageNum);
	});

	document.getElementById('pdf-fit')?.addEventListener('click', function () {
		if (!canvas || !wrap) return;
		var baseWidth = 800;
		scale = Math.max(0.5, wrap.clientWidth / baseWidth);
		renderPage(pageNum);
	});

	document.getElementById('pdf-fullscreen')?.addEventListener('click', function () {
		if (wrap.requestFullscreen) {
			wrap.requestFullscreen();
		}
	});
})();
