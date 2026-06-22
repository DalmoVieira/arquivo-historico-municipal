<?php
/**
 * Card reutilizável de documento.
 *
 * @package Arquivo_Historico
 */

$post_id      = get_the_ID();
$titulo       = get_the_title();
$descricao    = wp_trim_words( get_the_excerpt(), 24 );
$data         = get_post_meta( $post_id, '_data_documento', true );
$pdf          = get_post_meta( $post_id, '_arquivo_pdf', true );
$categoria    = get_the_terms( $post_id, 'categoria_doc' );
$thumb_custom = get_post_meta( $post_id, '_miniatura_documento', true );
?>
<article <?php post_class( 'document-card' ); ?>>
	<a href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy', 'alt' => esc_attr( $titulo ) ) ); ?>
		<?php elseif ( $thumb_custom ) : ?>
			<img src="<?php echo esc_url( $thumb_custom ); ?>" loading="lazy" alt="<?php echo esc_attr( $titulo ); ?>">
		<?php endif; ?>
	</a>
	<div class="document-card-body">
		<h3><a href="<?php the_permalink(); ?>"><?php echo esc_html( $titulo ); ?></a></h3>
		<p class="document-meta">
			<?php echo esc_html( $data ); ?>
			<?php if ( ! empty( $categoria ) && ! is_wp_error( $categoria ) ) : ?>
				| <?php echo esc_html( $categoria[0]->name ); ?>
			<?php endif; ?>
		</p>
		<p><?php echo esc_html( $descricao ); ?></p>
		<p>
			<a class="button" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Ver documento', 'arquivo-historico' ); ?></a>
			<?php if ( $pdf ) : ?>
				<a class="button" href="<?php echo esc_url( $pdf ); ?>" download><?php esc_html_e( 'Download', 'arquivo-historico' ); ?></a>
			<?php endif; ?>
		</p>
	</div>
</article>
