<?php
/**
 * Classe de registro e gestão do CPT Documento.
 *
 * @package Arquivo_Historico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Arquivo_Historico_CPT {
	/**
	 * Instância singleton.
	 *
	 * @var Arquivo_Historico_CPT|null
	 */
	private static $instance = null;

	/**
	 * Obtém a instância da classe.
	 *
	 * @return Arquivo_Historico_CPT
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Inicializa hooks da classe.
	 */
	public function init() {
		add_action( 'init', array( $this, 'registrar_cpt' ) );
		add_action( 'add_meta_boxes', array( $this, 'adicionar_meta_boxes' ) );
		add_action( 'save_post_documento', array( $this, 'salvar_meta_fields' ) );
		add_filter( 'manage_documento_posts_columns', array( $this, 'colunas_customizadas' ) );
		add_action( 'manage_documento_posts_custom_column', array( $this, 'preencher_colunas_customizadas' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'filtro_periodo_admin' ) );
		add_filter( 'parse_query', array( $this, 'aplicar_filtro_periodo_admin' ) );
		add_filter( 'bulk_actions-edit-documento', array( $this, 'registrar_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-documento', array( $this, 'processar_bulk_actions' ), 10, 3 );
	}

	/**
	 * Registra o custom post type documento.
	 */
	public function registrar_cpt() {
		$labels = array(
			'name'               => __( 'Documentos', 'arquivo-historico' ),
			'singular_name'      => __( 'Documento', 'arquivo-historico' ),
			'menu_name'          => __( 'Arquivo Histórico', 'arquivo-historico' ),
			'name_admin_bar'     => __( 'Documento', 'arquivo-historico' ),
			'add_new'            => __( 'Adicionar novo', 'arquivo-historico' ),
			'add_new_item'       => __( 'Adicionar novo documento', 'arquivo-historico' ),
			'new_item'           => __( 'Novo documento', 'arquivo-historico' ),
			'edit_item'          => __( 'Editar documento', 'arquivo-historico' ),
			'view_item'          => __( 'Ver documento', 'arquivo-historico' ),
			'all_items'          => __( 'Todos os documentos', 'arquivo-historico' ),
			'search_items'       => __( 'Buscar documentos', 'arquivo-historico' ),
			'not_found'          => __( 'Nenhum documento encontrado.', 'arquivo-historico' ),
			'not_found_in_trash' => __( 'Nenhum documento na lixeira.', 'arquivo-historico' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'has_archive'        => true,
			'rewrite'            => array( 'slug' => 'documentos' ),
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-media-document',
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'capability_type'    => 'post',
		);

		register_post_type( 'documento', $args );
	}

	/**
	 * Adiciona meta boxes no CPT.
	 */
	public function adicionar_meta_boxes() {
		add_meta_box(
			'arquivo_historico_meta_documento',
			__( 'Metadados do Documento', 'arquivo-historico' ),
			array( $this, 'render_meta_box' ),
			'documento',
			'normal',
			'high'
		);
	}

	/**
	 * Renderiza os campos de metadados.
	 *
	 * @param WP_Post $post Post atual.
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'arquivo_historico_salvar_documento', 'arquivo_historico_documento_nonce' );

		$campos = array(
			'_data_documento'      => get_post_meta( $post->ID, '_data_documento', true ),
			'_arquivo_pdf'         => get_post_meta( $post->ID, '_arquivo_pdf', true ),
			'_miniatura_documento' => get_post_meta( $post->ID, '_miniatura_documento', true ),
			'_autor_fonte'         => get_post_meta( $post->ID, '_autor_fonte', true ),
			'_palavras_chave'      => get_post_meta( $post->ID, '_palavras_chave', true ),
			'_periodo_historico'   => get_post_meta( $post->ID, '_periodo_historico', true ),
		);
		?>
		<p>
			<label for="_data_documento"><strong><?php esc_html_e( 'Data do Documento', 'arquivo-historico' ); ?></strong></label><br>
			<input type="date" id="_data_documento" name="_data_documento" value="<?php echo esc_attr( $campos['_data_documento'] ); ?>">
		</p>
		<p>
			<label for="_arquivo_pdf"><strong><?php esc_html_e( 'URL do PDF (Media Library)', 'arquivo-historico' ); ?></strong></label><br>
			<input type="url" id="_arquivo_pdf" name="_arquivo_pdf" value="<?php echo esc_url( $campos['_arquivo_pdf'] ); ?>" placeholder="https://">
		</p>
		<p>
			<button type="button" class="button" id="arquivo_historico_open_media"><?php esc_html_e( 'Selecionar PDF da Biblioteca', 'arquivo-historico' ); ?></button>
		</p>
		<p>
			<label for="_miniatura_documento"><strong><?php esc_html_e( 'URL da Miniatura', 'arquivo-historico' ); ?></strong></label><br>
			<input type="url" id="_miniatura_documento" name="_miniatura_documento" value="<?php echo esc_url( $campos['_miniatura_documento'] ); ?>" placeholder="https://">
		</p>
		<p>
			<label for="_autor_fonte"><strong><?php esc_html_e( 'Autor/Fonte', 'arquivo-historico' ); ?></strong></label><br>
			<input type="text" id="_autor_fonte" name="_autor_fonte" value="<?php echo esc_attr( $campos['_autor_fonte'] ); ?>">
		</p>
		<p>
			<label for="_palavras_chave"><strong><?php esc_html_e( 'Palavras-chave (separadas por vírgula)', 'arquivo-historico' ); ?></strong></label><br>
			<input type="text" id="_palavras_chave" name="_palavras_chave" value="<?php echo esc_attr( $campos['_palavras_chave'] ); ?>">
		</p>
		<p>
			<label for="_periodo_historico"><strong><?php esc_html_e( 'Período Histórico', 'arquivo-historico' ); ?></strong></label><br>
			<select id="_periodo_historico" name="_periodo_historico">
				<option value=""><?php esc_html_e( 'Selecione', 'arquivo-historico' ); ?></option>
				<?php for ( $ano = 1800; $ano <= 2020; $ano += 10 ) : ?>
					<?php $valor = $ano . '-' . ( $ano + 9 ); ?>
					<option value="<?php echo esc_attr( $valor ); ?>" <?php selected( $campos['_periodo_historico'], $valor ); ?>>
						<?php echo esc_html( $valor ); ?>
					</option>
				<?php endfor; ?>
			</select>
		</p>
		<hr>
		<p><strong><?php esc_html_e( 'Upload em lote (validação no navegador)', 'arquivo-historico' ); ?></strong></p>
		<p>
			<input type="file" id="arquivo_historico_bulk_files" accept="application/pdf" multiple>
		</p>
		<div id="arquivo_historico_bulk_status"></div>
		<?php
	}

	/**
	 * Salva os metadados do documento.
	 *
	 * @param int $post_id ID do post.
	 */
	public function salvar_meta_fields( $post_id ) {
		if ( ! isset( $_POST['arquivo_historico_documento_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['arquivo_historico_documento_nonce'] ) ), 'arquivo_historico_salvar_documento' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$campos = array(
			'_data_documento'      => 'sanitize_text_field',
			'_arquivo_pdf'         => 'esc_url_raw',
			'_miniatura_documento' => 'esc_url_raw',
			'_autor_fonte'         => 'sanitize_text_field',
			'_palavras_chave'      => 'sanitize_text_field',
			'_periodo_historico'   => 'sanitize_text_field',
		);

		foreach ( $campos as $campo => $sanitizador ) {
			if ( isset( $_POST[ $campo ] ) ) {
				$valor = call_user_func( $sanitizador, wp_unslash( $_POST[ $campo ] ) );
				if ( '_arquivo_pdf' === $campo && ! empty( $valor ) && 'pdf' !== strtolower( pathinfo( $valor, PATHINFO_EXTENSION ) ) ) {
					continue;
				}
				update_post_meta( $post_id, $campo, $valor );
			}
		}

		clean_post_cache( $post_id );
	}

	/**
	 * Colunas customizadas no admin.
	 *
	 * @param array $columns Colunas.
	 * @return array
	 */
	public function colunas_customizadas( $columns ) {
		$columns['data_documento'] = __( 'Data', 'arquivo-historico' );
		$columns['periodo']        = __( 'Período', 'arquivo-historico' );
		$columns['download']       = __( 'Download', 'arquivo-historico' );
		return $columns;
	}

	/**
	 * Preenche colunas customizadas.
	 *
	 * @param string $column Nome da coluna.
	 * @param int    $post_id ID do post.
	 */
	public function preencher_colunas_customizadas( $column, $post_id ) {
		if ( 'data_documento' === $column ) {
			echo esc_html( get_post_meta( $post_id, '_data_documento', true ) );
		}

		if ( 'periodo' === $column ) {
			echo esc_html( get_post_meta( $post_id, '_periodo_historico', true ) );
		}

		if ( 'download' === $column ) {
			$pdf = get_post_meta( $post_id, '_arquivo_pdf', true );
			if ( $pdf ) {
				echo '<a href="' . esc_url( $pdf ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Baixar', 'arquivo-historico' ) . '</a>';
			}
		}
	}

	/**
	 * Adiciona filtro por período na listagem admin.
	 */
	public function filtro_periodo_admin() {
		global $typenow;
		if ( 'documento' !== $typenow ) {
			return;
		}

		$periodo_atual = isset( $_GET['filtro_periodo_doc'] ) ? sanitize_text_field( wp_unslash( $_GET['filtro_periodo_doc'] ) ) : '';
		echo '<select name="filtro_periodo_doc">';
		echo '<option value="">' . esc_html__( 'Todos os períodos', 'arquivo-historico' ) . '</option>';
		for ( $ano = 1800; $ano <= 2020; $ano += 10 ) {
			$valor = $ano . '-' . ( $ano + 9 );
			echo '<option value="' . esc_attr( $valor ) . '" ' . selected( $periodo_atual, $valor, false ) . '>' . esc_html( $valor ) . '</option>';
		}
		echo '</select>';
	}

	/**
	 * Aplica filtro na query da listagem admin.
	 *
	 * @param WP_Query $query Query.
	 */
	public function aplicar_filtro_periodo_admin( $query ) {
		global $pagenow;

		if ( 'edit.php' !== $pagenow || ! is_admin() ) {
			return;
		}

		if ( isset( $_GET['post_type'], $_GET['filtro_periodo_doc'] ) && 'documento' === $_GET['post_type'] && '' !== $_GET['filtro_periodo_doc'] ) {
			$periodo = sanitize_text_field( wp_unslash( $_GET['filtro_periodo_doc'] ) );
			$query->set(
				'meta_query',
				array(
					array(
						'key'   => '_periodo_historico',
						'value' => $periodo,
					),
				)
			);
			$query->set( 'posts_per_page', 50 );
		}
	}

	/**
	 * Registra bulk action customizada.
	 *
	 * @param array $actions Ações.
	 * @return array
	 */
	public function registrar_bulk_actions( $actions ) {
		$actions['arquivo_historico_upload_lote'] = __( 'Preparar upload em lote', 'arquivo-historico' );
		return $actions;
	}

	/**
	 * Processa bulk action customizada.
	 *
	 * @param string $redirect_to URL de redirecionamento.
	 * @param string $action Ação.
	 * @param array  $post_ids IDs dos posts.
	 * @return string
	 */
	public function processar_bulk_actions( $redirect_to, $action, $post_ids ) {
		if ( 'arquivo_historico_upload_lote' !== $action ) {
			return $redirect_to;
		}

		$redirect_to = add_query_arg(
			array(
				'arquivo_historico_bulk' => count( $post_ids ),
			),
			$redirect_to
		);

		return $redirect_to;
	}
}
