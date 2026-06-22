<?php
/**
 * Classe para registro de taxonomias.
 *
 * @package Arquivo_Historico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Arquivo_Historico_Taxonomias {
	/**
	 * Instância singleton.
	 *
	 * @var Arquivo_Historico_Taxonomias|null
	 */
	private static $instance = null;

	/**
	 * Obtém instância.
	 *
	 * @return Arquivo_Historico_Taxonomias
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Inicializa hooks.
	 */
	public function init() {
		add_action( 'init', array( $this, 'registrar_taxonomias' ) );
	}

	/**
	 * Registra as taxonomias do acervo.
	 */
	public function registrar_taxonomias() {
		$this->registrar_categoria_doc();
		$this->registrar_periodo_doc();
		$this->registrar_tema_doc();
	}

	/**
	 * Taxonomia categoria_doc.
	 */
	private function registrar_categoria_doc() {
		$labels = array(
			'name'          => __( 'Categorias', 'arquivo-historico' ),
			'singular_name' => __( 'Categoria', 'arquivo-historico' ),
			'search_items'  => __( 'Buscar categorias', 'arquivo-historico' ),
			'all_items'     => __( 'Todas as categorias', 'arquivo-historico' ),
			'edit_item'     => __( 'Editar categoria', 'arquivo-historico' ),
			'update_item'   => __( 'Atualizar categoria', 'arquivo-historico' ),
			'add_new_item'  => __( 'Adicionar categoria', 'arquivo-historico' ),
			'new_item_name' => __( 'Nova categoria', 'arquivo-historico' ),
			'menu_name'     => __( 'Categorias', 'arquivo-historico' ),
		);

		register_taxonomy(
			'categoria_doc',
			'documento',
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'categoria-documento' ),
				'show_in_rest'      => true,
			)
		);

		$categorias = array( 'Política', 'Educação', 'Saúde', 'Urbanismo', 'Cultura', 'Economia', 'Meio Ambiente' );
		foreach ( $categorias as $categoria ) {
			if ( ! term_exists( $categoria, 'categoria_doc' ) ) {
				wp_insert_term( $categoria, 'categoria_doc' );
			}
		}
	}

	/**
	 * Taxonomia periodo_doc.
	 */
	private function registrar_periodo_doc() {
		$labels = array(
			'name'          => __( 'Períodos', 'arquivo-historico' ),
			'singular_name' => __( 'Período', 'arquivo-historico' ),
			'menu_name'     => __( 'Períodos', 'arquivo-historico' ),
		);

		register_taxonomy(
			'periodo_doc',
			'documento',
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'periodo-documento' ),
				'show_in_rest'      => true,
			)
		);

		for ( $ano = 1800; $ano <= 2020; $ano += 10 ) {
			$decada = $ano . '-' . ( $ano + 9 );
			if ( ! term_exists( $decada, 'periodo_doc' ) ) {
				wp_insert_term( $decada, 'periodo_doc' );
			}
		}
	}

	/**
	 * Taxonomia tema_doc.
	 */
	private function registrar_tema_doc() {
		$labels = array(
			'name'          => __( 'Temas', 'arquivo-historico' ),
			'singular_name' => __( 'Tema', 'arquivo-historico' ),
			'menu_name'     => __( 'Temas', 'arquivo-historico' ),
		);

		register_taxonomy(
			'tema_doc',
			'documento',
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'rewrite'           => array( 'slug' => 'tema-documento' ),
				'show_in_rest'      => true,
			)
		);
	}
}
