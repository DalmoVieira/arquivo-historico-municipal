<?php
/**
 * Template de resultados de busca.
 *
 * @package Arquivo_Historico
 */

get_header();
global $wp_query;
$termo = get_search_query();
?>
<section class="hero">
	<h1><?php esc_html_e( 'Resultados da busca', 'arquivo-historico' ); ?></h1>
	<p><?php printf( esc_html__( 'Termo pesquisado: %s', 'arquivo-historico' ), '<strong>' . esc_html( $termo ) . '</strong>' ); ?></p>
</section>

<?php get_template_part( 'template-parts/search-form' ); ?>

<?php if ( have_posts() ) : ?>
	<p><?php echo esc_html( $wp_query->found_posts ); ?> <?php esc_html_e( 'resultados encontrados.', 'arquivo-historico' ); ?></p>
	<div class="document-grid">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php get_template_part( 'template-parts/document-card' ); ?>
		<?php endwhile; ?>
	</div>
	<div class="pagination"><?php the_posts_pagination(); ?></div>
<?php else : ?>
	<div class="empty-state"><?php esc_html_e( 'Nenhum resultado para sua busca.', 'arquivo-historico' ); ?></div>
<?php endif; ?>

<?php
get_footer();
