<?php
/**
 * Template de arquivo do CPT documento.
 *
 * @package Arquivo_Historico
 */

get_header();
?>
<section class="hero">
	<h1><?php post_type_archive_title(); ?></h1>
	<p><?php esc_html_e( 'Explore o acervo histórico municipal utilizando busca e filtros facetados.', 'arquivo-historico' ); ?></p>
</section>

<div class="main-layout">
	<?php get_sidebar(); ?>
	<section>
		<div class="filters">
			<button type="button" class="js-view-toggle" data-view="grid"><?php esc_html_e( 'Grade', 'arquivo-historico' ); ?></button>
			<button type="button" class="js-view-toggle" data-view="list"><?php esc_html_e( 'Lista', 'arquivo-historico' ); ?></button>
			<select id="ordem_documentos" name="ordem_documentos">
				<option value="date_desc"><?php esc_html_e( 'Mais recente', 'arquivo-historico' ); ?></option>
				<option value="date_asc"><?php esc_html_e( 'Mais antigo', 'arquivo-historico' ); ?></option>
				<option value="relevancia"><?php esc_html_e( 'Relevância', 'arquivo-historico' ); ?></option>
			</select>
		</div>

		<div id="resultados-documentos" class="document-grid">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/document-card' ); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="empty-state"><?php esc_html_e( 'Nenhum documento encontrado.', 'arquivo-historico' ); ?></div>
			<?php endif; ?>
		</div>

		<div class="pagination">
			<?php the_posts_pagination(); ?>
		</div>
	</section>
</div>
<script>
window.arquivoHistoricoArchiveInit = true;
</script>
<?php
get_footer();
