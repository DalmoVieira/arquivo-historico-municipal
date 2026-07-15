<?php
/**
 * Schema.org e metatags sociais.
 *
 * @package Arquivo_Historico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exibe metadados estruturados na página de documento individual.
 */
function arquivo_historico_schema_output() {
	if ( ! is_singular( 'documento' ) ) {
		return;
	}

	$post_id = get_the_ID();
	$pdf_url = get_post_meta( $post_id, '_arquivo_pdf', true );
	$imagem  = get_the_post_thumbnail_url( $post_id, 'large' );
	$schema  = array(
		'@context'          => 'https://schema.org',
		'@type'             => 'DigitalDocument',
		'name'              => get_the_title( $post_id ),
		'description'       => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
		'datePublished'     => get_the_date( 'c', $post_id ),
		'encodingFormat'    => 'application/pdf',
		'contentUrl'        => esc_url_raw( $pdf_url ),
		'isPartOf'          => array(
			'@type' => 'ArchiveComponent',
			'name'  => get_bloginfo( 'name' ),
		),
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
	echo '<meta property="og:type" content="article">';
	echo '<meta property="og:title" content="' . esc_attr( get_the_title( $post_id ) ) . '">';
	echo '<meta property="og:description" content="' . esc_attr( wp_strip_all_tags( get_the_excerpt( $post_id ) ) ) . '">';
	echo '<meta property="og:url" content="' . esc_url( get_permalink( $post_id ) ) . '">';
	if ( $imagem ) {
		echo '<meta property="og:image" content="' . esc_url( $imagem ) . '">';
	}
	echo '<meta name="twitter:card" content="summary_large_image">';
	echo '<meta name="twitter:title" content="' . esc_attr( get_the_title( $post_id ) ) . '">';
	echo '<meta name="twitter:description" content="' . esc_attr( wp_strip_all_tags( get_the_excerpt( $post_id ) ) ) . '">';
}
add_action( 'wp_head', 'arquivo_historico_schema_output' );
