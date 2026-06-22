<?php
/**
 * Cabeçalho do tema.
 *
 * @package Arquivo_Historico
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#conteudo-principal"><?php esc_html_e( 'Ir para o conteúdo', 'arquivo-historico' ); ?></a>
<header class="site-header">
	<div class="container header-inner">
		<div>
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<p><?php bloginfo( 'description' ); ?></p>
		</div>
		<nav aria-label="<?php esc_attr_e( 'Menu principal', 'arquivo-historico' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'principal',
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
		<div class="search-inline" role="search" aria-label="<?php esc_attr_e( 'Busca rápida', 'arquivo-historico' ); ?>">
			<?php get_search_form(); ?>
		</div>
	</div>
</header>
<main id="conteudo-principal" class="container">
