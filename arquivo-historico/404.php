<?php
/**
 * Template 404.
 *
 * @package Arquivo_Historico
 */

get_header();
?>
<section class="hero">
	<h1><?php esc_html_e( 'Página não encontrada', 'arquivo-historico' ); ?></h1>
	<p><?php esc_html_e( 'O conteúdo solicitado não foi localizado. Utilize a busca para encontrar documentos.', 'arquivo-historico' ); ?></p>
</section>
<?php get_search_form(); ?>
<?php
get_footer();
