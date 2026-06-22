<?php
/**
 * Template fallback/index.
 *
 * @package Arquivo_Historico
 */

get_header();
?>
<section class="hero">
	<h1><?php esc_html_e( 'Documentos em destaque', 'arquivo-historico' ); ?></h1>
	<p><?php esc_html_e( 'Consulte os documentos históricos mais recentes publicados.', 'arquivo-historico' ); ?></p>
</section>
<div class="document-grid">
	<?php
	$destacados = new WP_Query(
		array(
			'post_type'           => 'documento',
			'posts_per_page'      => 6,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
		)
	);
	if ( $destacados->have_posts() ) :
		while ( $destacados->have_posts() ) :
			$destacados->the_post();
			get_template_part( 'template-parts/document-card' );
		endwhile;
		wp_reset_postdata();
	else :
		?>
		<p><?php esc_html_e( 'Ainda não há documentos cadastrados.', 'arquivo-historico' ); ?></p>
		<?php
	endif;
	?>
</div>
<?php
get_footer();
