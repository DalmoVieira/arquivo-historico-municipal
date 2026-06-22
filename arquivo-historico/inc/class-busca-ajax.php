<?php
/**
 * Classe de busca AJAX de documentos.
 *
 * @package Arquivo_Historico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Arquivo_Historico_Busca {
	/**
	 * Instância singleton.
	 *
	 * @var Arquivo_Historico_Busca|null
	 */
	private static $instance = null;

	/**
	 * Obtém instância.
	 *
	 * @return Arquivo_Historico_Busca
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
		add_action( 'wp_ajax_busca_documentos', array( $this, 'busca_documentos' ) );
		add_action( 'wp_ajax_nopriv_busca_documentos', array( $this, 'busca_documentos' ) );
		add_action( 'wp_ajax_autocomplete_busca', array( $this, 'autocomplete_busca' ) );
		add_action( 'wp_ajax_nopriv_autocomplete_busca', array( $this, 'autocomplete_busca' ) );
		add_action( 'save_post_documento', array( $this, 'limpar_cache_busca' ) );
	}

	/**
	 * Handler de busca AJAX com filtros.
	 */
	public function busca_documentos() {
		$this->validar_nonce();

		$termo    = isset( $_POST['termo'] ) ? sanitize_text_field( wp_unslash( $_POST['termo'] ) ) : '';
		$categoria = isset( $_POST['categoria'] ) ? sanitize_text_field( wp_unslash( $_POST['categoria'] ) ) : '';
		$periodo   = isset( $_POST['periodo'] ) ? sanitize_text_field( wp_unslash( $_POST['periodo'] ) ) : '';
		$tema      = isset( $_POST['tema'] ) ? sanitize_text_field( wp_unslash( $_POST['tema'] ) ) : '';
		$ordem     = isset( $_POST['ordem'] ) ? sanitize_text_field( wp_unslash( $_POST['ordem'] ) ) : 'date_desc';
		$pagina    = isset( $_POST['pagina'] ) ? max( 1, absint( $_POST['pagina'] ) ) : 1;

		$cache_key = 'ah_busca_' . md5( wp_json_encode( array( $termo, $categoria, $periodo, $tema, $ordem, $pagina ) ) );
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			wp_send_json_success( $cached );
		}

		$args = array(
			'post_type'      => 'documento',
			'post_status'    => 'publish',
			's'              => $termo,
			'paged'          => $pagina,
			'posts_per_page' => 20,
		);

		$args = $this->aplicar_ordenacao( $args, $ordem );

		$tax_query = array( 'relation' => 'AND' );
		if ( $categoria ) {
			$tax_query[] = array(
				'taxonomy' => 'categoria_doc',
				'field'    => 'slug',
				'terms'    => $categoria,
			);
		}
		if ( $periodo ) {
			$tax_query[] = array(
				'taxonomy' => 'periodo_doc',
				'field'    => 'slug',
				'terms'    => $periodo,
			);
		}
		if ( $tema ) {
			$tax_query[] = array(
				'taxonomy' => 'tema_doc',
				'field'    => 'slug',
				'terms'    => $tema,
			);
		}
		if ( count( $tax_query ) > 1 ) {
			$args['tax_query'] = $tax_query;
		}

		if ( $termo ) {
			$args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key'     => '_autor_fonte',
					'value'   => $termo,
					'compare' => 'LIKE',
				),
				array(
					'key'     => '_palavras_chave',
					'value'   => $termo,
					'compare' => 'LIKE',
				),
			);
		}

		$query = new WP_Query( $args );

		$documentos = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$titulo = get_the_title();
			$resumo = get_the_excerpt();
			$documentos[] = array(
				'id'      => get_the_ID(),
				'titulo'  => $this->destacar_termo( $titulo, $termo ),
				'resumo'  => $this->destacar_termo( $resumo, $termo ),
				'link'    => get_permalink(),
				'data'    => get_post_meta( get_the_ID(), '_data_documento', true ),
				'pdf'     => get_post_meta( get_the_ID(), '_arquivo_pdf', true ),
				'thumb'   => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
			);
		}
		wp_reset_postdata();

		$retorno = array(
			'documentos'        => $documentos,
			'total'             => (int) $query->found_posts,
			'paginas'           => (int) $query->max_num_pages,
			'termo_destacado'   => $termo,
			'pagina_atual'      => $pagina,
		);

		set_transient( $cache_key, $retorno, HOUR_IN_SECONDS );
		wp_send_json_success( $retorno );
	}

	/**
	 * Handler de autocomplete.
	 */
	public function autocomplete_busca() {
		$this->validar_nonce();
		$termo = isset( $_POST['termo'] ) ? sanitize_text_field( wp_unslash( $_POST['termo'] ) ) : '';

		if ( strlen( $termo ) < 2 ) {
			wp_send_json_success( array() );
		}

		$query = new WP_Query(
			array(
				'post_type'      => 'documento',
				'post_status'    => 'publish',
				's'              => $termo,
				'posts_per_page' => 8,
				'no_found_rows'  => true,
			)
		);

		$sugestoes = array();
		foreach ( $query->posts as $post ) {
			$sugestoes[] = array(
				'id'    => $post->ID,
				'texto' => get_the_title( $post->ID ),
				'url'   => get_permalink( $post->ID ),
			);
		}

		wp_send_json_success( $sugestoes );
	}

	/**
	 * Limpa cache de busca quando documento é atualizado.
	 */
	public function limpar_cache_busca() {
		global $wpdb;
		$like_data    = $wpdb->esc_like( '_transient_ah_busca_' ) . '%';
		$like_timeout = $wpdb->esc_like( '_transient_timeout_ah_busca_' ) . '%';
		$sql          = $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", $like_data, $like_timeout );
		$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Aplica ordenação conforme opção.
	 *
	 * @param array  $args Argumentos da query.
	 * @param string $ordem Ordem.
	 * @return array
	 */
	private function aplicar_ordenacao( $args, $ordem ) {
		switch ( $ordem ) {
			case 'date_asc':
				$args['orderby'] = 'date';
				$args['order']   = 'ASC';
				break;
			case 'relevancia':
				$args['orderby'] = 'relevance';
				$args['order']   = 'DESC';
				break;
			case 'date_desc':
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}
		return $args;
	}

	/**
	 * Destaca termo com tag mark.
	 *
	 * @param string $texto Texto.
	 * @param string $termo Termo.
	 * @return string
	 */
	private function destacar_termo( $texto, $termo ) {
		$texto_esc = esc_html( $texto );
		if ( ! $termo ) {
			return $texto_esc;
		}

		return preg_replace( '/(' . preg_quote( $termo, '/' ) . ')/i', '<mark>$1</mark>', $texto_esc );
	}

	/**
	 * Valida nonce padrão.
	 */
	private function validar_nonce() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'arquivo_historico_busca_nonce' ) ) {
			wp_send_json_error( array( 'mensagem' => __( 'Falha de segurança na requisição.', 'arquivo-historico' ) ), 403 );
		}
	}
}
