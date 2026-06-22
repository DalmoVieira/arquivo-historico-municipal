<?php
/**
 * Funções principais do tema Arquivo Histórico.
 *
 * @package Arquivo_Historico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$arquivo_historico_includes = array(
	'/inc/class-documento-cpt.php',
	'/inc/class-taxonomias.php',
	'/inc/class-busca-ajax.php',
	'/inc/class-upload-handler.php',
	'/inc/schema-org.php',
);

foreach ( $arquivo_historico_includes as $arquivo_historico_include ) {
	$arquivo_historico_path = get_template_directory() . $arquivo_historico_include;
	if ( file_exists( $arquivo_historico_path ) ) {
		require_once $arquivo_historico_path;
	}
}

/**
 * Configurações do tema.
 */
function arquivo_historico_theme_setup() {
	load_theme_textdomain( 'arquivo-historico', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'principal' => esc_html__( 'Menu Principal', 'arquivo-historico' ),
			'rodape'    => esc_html__( 'Menu de Rodapé', 'arquivo-historico' ),
		)
	);
}
add_action( 'after_setup_theme', 'arquivo_historico_theme_setup' );

/**
 * Enfileira scripts e estilos.
 */
function arquivo_historico_enqueue_assets() {
	$versao = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'arquivo-historico-style', get_stylesheet_uri(), array(), $versao );
	wp_enqueue_style( 'arquivo-historico-pdf-viewer', get_template_directory_uri() . '/assets/css/pdf-viewer.css', array(), $versao );

	if ( is_admin() ) {
		wp_enqueue_style( 'arquivo-historico-admin', get_template_directory_uri() . '/assets/css/admin.css', array(), $versao );
	}

	wp_enqueue_script( 'pdfjs', 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.5.136/pdf.min.js', array(), '4.5.136', true );

	wp_enqueue_script( 'arquivo-historico-search', get_template_directory_uri() . '/assets/js/search.js', array( 'jquery' ), $versao, true );
	wp_enqueue_script( 'arquivo-historico-pdf-viewer', get_template_directory_uri() . '/assets/js/pdf-viewer.js', array(), $versao, true );

	if ( is_admin() ) {
		wp_enqueue_script( 'arquivo-historico-admin-upload', get_template_directory_uri() . '/assets/js/admin-upload.js', array( 'jquery' ), $versao, true );
	}

	wp_localize_script(
		'arquivo-historico-search',
		'arquivoHistoricoData',
		array(
			'ajaxurl' => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
			'nonce'   => wp_create_nonce( 'arquivo_historico_busca_nonce' ),
			'i18n'    => array(
				'loading' => esc_html__( 'Carregando resultados...', 'arquivo-historico' ),
				'empty'   => esc_html__( 'Nenhum documento encontrado.', 'arquivo-historico' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'arquivo_historico_enqueue_assets' );
add_action( 'admin_enqueue_scripts', 'arquivo_historico_enqueue_assets' );

/**
 * Tipos MIME aceitos.
 *
 * @param array $mimes Tipos atuais.
 * @return array
 */
function arquivo_historico_filter_upload_mimes( $mimes ) {
	$mimes['pdf'] = 'application/pdf';
	return apply_filters( 'arquivo_historico_mimes_aceitos', $mimes );
}
add_filter( 'upload_mimes', 'arquivo_historico_filter_upload_mimes' );

/**
 * Tamanho máximo de upload customizável.
 *
 * @return int
 */
function arquivo_historico_max_upload_size() {
	$padrao = 50 * 1024 * 1024;
	return (int) apply_filters( 'arquivo_historico_max_upload_size', $padrao );
}

/**
 * Compatibilidade com page builders.
 */
function arquivo_historico_builder_compatibility() {
	if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
		wp_dequeue_style( 'arquivo-historico-style' );
	}

	if ( function_exists( 'et_builder_is_product_tour_enabled' ) && et_builder_is_product_tour_enabled() ) {
		wp_dequeue_style( 'arquivo-historico-style' );
	}
}
add_action( 'wp_enqueue_scripts', 'arquivo_historico_builder_compatibility', 99 );

/**
 * Inicializa classes do tema.
 */
function arquivo_historico_init_classes() {
	if ( class_exists( 'Arquivo_Historico_CPT' ) ) {
		Arquivo_Historico_CPT::get_instance()->init();
	}
	if ( class_exists( 'Arquivo_Historico_Taxonomias' ) ) {
		Arquivo_Historico_Taxonomias::get_instance()->init();
	}
	if ( class_exists( 'Arquivo_Historico_Busca' ) ) {
		Arquivo_Historico_Busca::get_instance()->init();
	}
	if ( class_exists( 'Arquivo_Historico_Upload' ) ) {
		Arquivo_Historico_Upload::get_instance()->init();
	}
}
add_action( 'after_setup_theme', 'arquivo_historico_init_classes', 20 );
