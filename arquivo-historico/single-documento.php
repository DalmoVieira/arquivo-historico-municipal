<?php
/**
 * Template individual do documento.
 *
 * @package Arquivo_Historico
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$post_id   = get_the_ID();
		$data      = get_post_meta( $post_id, '_data_documento', true );
		$pdf       = get_post_meta( $post_id, '_arquivo_pdf', true );
		$autor     = get_post_meta( $post_id, '_autor_fonte', true );
		$palavras  = get_post_meta( $post_id, '_palavras_chave', true );
		$periodo   = get_post_meta( $post_id, '_periodo_historico', true );
		$cat_terms = get_the_terms( $post_id, 'categoria_doc' );
		?>
		<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'arquivo-historico' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'arquivo-historico' ); ?></a>
			&raquo;
			<?php if ( ! empty( $cat_terms ) && ! is_wp_error( $cat_terms ) ) : ?>
				<a href="<?php echo esc_url( get_term_link( $cat_terms[0] ) ); ?>"><?php echo esc_html( $cat_terms[0]->name ); ?></a>
				&raquo;
			<?php endif; ?>
			<span><?php the_title(); ?></span>
		</nav>

		<article <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<p class="document-meta">
				<strong><?php esc_html_e( 'Data:', 'arquivo-historico' ); ?></strong> <?php echo esc_html( $data ); ?> |
				<strong><?php esc_html_e( 'Autor/Fonte:', 'arquivo-historico' ); ?></strong> <?php echo esc_html( $autor ); ?> |
				<strong><?php esc_html_e( 'Período:', 'arquivo-historico' ); ?></strong> <?php echo esc_html( $periodo ); ?>
			</p>
			<p><strong><?php esc_html_e( 'Palavras-chave:', 'arquivo-historico' ); ?></strong> <?php echo esc_html( $palavras ); ?></p>
			<div><?php the_content(); ?></div>

			<?php get_template_part( 'template-parts/pdf-viewer' ); ?>

			<?php if ( $pdf ) : ?>
				<p><a class="button" href="<?php echo esc_url( $pdf ); ?>" download><?php esc_html_e( 'Download do PDF', 'arquivo-historico' ); ?></a></p>
			<?php endif; ?>

			<div class="share-buttons">
				<a class="button social-btn" target="_blank" rel="noopener noreferrer" href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink() ); ?>"><?php esc_html_e( 'Twitter/X', 'arquivo-historico' ); ?></a>
				<a class="button social-btn" target="_blank" rel="noopener noreferrer" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( get_permalink() ); ?>"><?php esc_html_e( 'Facebook', 'arquivo-historico' ); ?></a>
				<a class="button social-btn" target="_blank" rel="noopener noreferrer" href="https://wa.me/?text=<?php echo rawurlencode( get_permalink() ); ?>"><?php esc_html_e( 'WhatsApp', 'arquivo-historico' ); ?></a>
				<a class="button social-btn" target="_blank" rel="noopener noreferrer" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode( get_permalink() ); ?>"><?php esc_html_e( 'LinkedIn', 'arquivo-historico' ); ?></a>
				<button class="button social-btn" type="button" data-copy-link="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Copiar link', 'arquivo-historico' ); ?></button>
			</div>
		</article>

		<section>
			<h2><?php esc_html_e( 'Documentos relacionados', 'arquivo-historico' ); ?></h2>
			<div class="document-grid">
				<?php
				$ids_categorias = ! empty( $cat_terms ) && ! is_wp_error( $cat_terms ) ? wp_list_pluck( $cat_terms, 'term_id' ) : array();
				$relacionados   = new WP_Query(
					array(
						'post_type'           => 'documento',
						'posts_per_page'      => 3,
						'post__not_in'        => array( $post_id ),
						'no_found_rows'       => true,
						'ignore_sticky_posts' => true,
						'tax_query'           => ! empty( $ids_categorias ) ? array(
							array(
								'taxonomy' => 'categoria_doc',
								'field'    => 'term_id',
								'terms'    => $ids_categorias,
							),
						) : array(),
					)
				);
				if ( $relacionados->have_posts() ) :
					while ( $relacionados->have_posts() ) :
						$relacionados->the_post();
						get_template_part( 'template-parts/document-card' );
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<p><?php esc_html_e( 'Nenhum documento relacionado encontrado.', 'arquivo-historico' ); ?></p>
					<?php
				endif;
				?>
			</div>
		</section>
		<?php
	endwhile;
endif;

get_footer();
