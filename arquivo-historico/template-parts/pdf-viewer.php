<?php
/**
 * Template part do visualizador PDF.
 *
 * @package Arquivo_Historico
 */

$pdf_url = get_post_meta( get_the_ID(), '_arquivo_pdf', true );
if ( ! $pdf_url ) {
	return;
}
?>
<div class="viewer-wrap" data-pdf-url="<?php echo esc_url( $pdf_url ); ?>">
	<div class="pdf-controls" aria-label="<?php esc_attr_e( 'Controles do PDF', 'arquivo-historico' ); ?>">
		<button type="button" id="pdf-prev" aria-label="<?php esc_attr_e( 'Página anterior', 'arquivo-historico' ); ?>"><?php esc_html_e( 'Anterior', 'arquivo-historico' ); ?></button>
		<button type="button" id="pdf-next" aria-label="<?php esc_attr_e( 'Próxima página', 'arquivo-historico' ); ?>"><?php esc_html_e( 'Próxima', 'arquivo-historico' ); ?></button>
		<button type="button" id="pdf-zoom-out" aria-label="<?php esc_attr_e( 'Diminuir zoom', 'arquivo-historico' ); ?>">-</button>
		<button type="button" id="pdf-zoom-in" aria-label="<?php esc_attr_e( 'Aumentar zoom', 'arquivo-historico' ); ?>">+</button>
		<button type="button" id="pdf-fit" aria-label="<?php esc_attr_e( 'Ajustar largura', 'arquivo-historico' ); ?>"><?php esc_html_e( 'Ajustar', 'arquivo-historico' ); ?></button>
		<button type="button" id="pdf-fullscreen" aria-label="<?php esc_attr_e( 'Tela cheia', 'arquivo-historico' ); ?>"><?php esc_html_e( 'Tela cheia', 'arquivo-historico' ); ?></button>
		<span id="pdf-page-indicator" aria-live="polite"></span>
	</div>
	<div id="pdf-loading" class="loading-state"><?php esc_html_e( 'Carregando PDF...', 'arquivo-historico' ); ?></div>
	<canvas id="pdf-render"></canvas>
	<noscript>
		<p><a href="<?php echo esc_url( $pdf_url ); ?>"><?php esc_html_e( 'Seu navegador não suporta visualização embutida. Clique para baixar o PDF.', 'arquivo-historico' ); ?></a></p>
	</noscript>
</div>
