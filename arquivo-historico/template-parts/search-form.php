<?php
/**
 * Formulário avançado de busca.
 *
 * @package Arquivo_Historico
 */

$categorias = get_terms(
	array(
		'taxonomy'   => 'categoria_doc',
		'hide_empty' => false,
	)
);
$periodos   = get_terms(
	array(
		'taxonomy'   => 'periodo_doc',
		'hide_empty' => false,
	)
);
$temas      = get_terms(
	array(
		'taxonomy'   => 'tema_doc',
		'hide_empty' => false,
	)
);
?>
<form id="arquivo-historico-search-form" class="search-advanced" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<?php wp_nonce_field( 'arquivo_historico_busca_nonce', 'arquivo_historico_busca_form_nonce' ); ?>
	<input type="hidden" name="post_type" value="documento">
	<input type="search" id="termo_busca" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Buscar por palavra-chave...', 'arquivo-historico' ); ?>" autocomplete="off">
	<div id="autocomplete-list" aria-live="polite"></div>

	<select name="categoria" id="filtro_categoria">
		<option value=""><?php esc_html_e( 'Todas as categorias', 'arquivo-historico' ); ?></option>
		<?php foreach ( $categorias as $categoria ) : ?>
			<option value="<?php echo esc_attr( $categoria->slug ); ?>"><?php echo esc_html( $categoria->name ); ?></option>
		<?php endforeach; ?>
	</select>

	<select name="periodo" id="filtro_periodo">
		<option value=""><?php esc_html_e( 'Todos os períodos', 'arquivo-historico' ); ?></option>
		<?php foreach ( $periodos as $periodo ) : ?>
			<option value="<?php echo esc_attr( $periodo->slug ); ?>"><?php echo esc_html( $periodo->name ); ?></option>
		<?php endforeach; ?>
	</select>

	<select name="tema" id="filtro_tema">
		<option value=""><?php esc_html_e( 'Todos os temas', 'arquivo-historico' ); ?></option>
		<?php foreach ( $temas as $tema ) : ?>
			<option value="<?php echo esc_attr( $tema->slug ); ?>"><?php echo esc_html( $tema->name ); ?></option>
		<?php endforeach; ?>
	</select>

	<button type="submit"><?php esc_html_e( 'Buscar', 'arquivo-historico' ); ?></button>
</form>
