<?php
/**
 * Classe de validação de upload.
 *
 * @package Arquivo_Historico
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Arquivo_Historico_Upload {
	/**
	 * Instância singleton.
	 *
	 * @var Arquivo_Historico_Upload|null
	 */
	private static $instance = null;

	/**
	 * Obtém instância.
	 *
	 * @return Arquivo_Historico_Upload
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
		add_filter( 'wp_handle_upload_prefilter', array( $this, 'validar_upload_pdf' ) );
		add_action( 'admin_init', array( $this, 'garantir_htaccess_uploads' ) );
	}

	/**
	 * Valida o upload para PDF e tamanho.
	 *
	 * @param array $file Arquivo em upload.
	 * @return array
	 */
	public function validar_upload_pdf( $file ) {
		if ( empty( $file['name'] ) ) {
			return $file;
		}

		$extensao = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
		$max_size = function_exists( 'arquivo_historico_max_upload_size' ) ? arquivo_historico_max_upload_size() : 50 * 1024 * 1024;

		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime  = $finfo ? finfo_file( $finfo, $file['tmp_name'] ) : '';
		if ( $finfo ) {
			finfo_close( $finfo );
		}

		if ( 'pdf' !== $extensao || 'application/pdf' !== $mime ) {
			$file['error'] = __( 'Somente arquivos PDF são permitidos.', 'arquivo-historico' );
			return $file;
		}

		if ( (int) $file['size'] > (int) $max_size ) {
			$file['error'] = __( 'O arquivo excede o limite de 50MB.', 'arquivo-historico' );
		}

		return $file;
	}

	/**
	 * Cria .htaccess para proteger diretório de uploads temáticos.
	 */
	public function garantir_htaccess_uploads() {
		$uploads = wp_upload_dir();
		$dir     = trailingslashit( $uploads['basedir'] ) . 'arquivo-historico';
		$file    = trailingslashit( $dir ) . '.htaccess';

		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		if ( ! file_exists( $file ) && is_writable( $dir ) ) {
			$conteudo  = "<FilesMatch \\.(php|phtml|php3|php4|php5|php7|php8|phar)$>\nDeny from all\n</FilesMatch>\n";
			$escreveu  = file_put_contents( $file, $conteudo );
			if ( false === $escreveu ) {
				error_log( 'Arquivo Histórico: falha ao gerar .htaccess de proteção em uploads.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		}
	}
}
