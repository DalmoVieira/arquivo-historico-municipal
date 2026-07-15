<?php
/**
 * Rodapé do tema.
 *
 * @package Arquivo_Historico
 */

?>
</main>
<footer class="site-footer">
	<div class="container footer-inner">
		<div>
			<p><strong><?php esc_html_e( 'Arquivo Histórico Municipal', 'arquivo-historico' ); ?></strong></p>
			<p><?php esc_html_e( 'Endereço: Praça Central, 100 - Centro', 'arquivo-historico' ); ?></p>
			<p><?php esc_html_e( 'Contato: arquivo@municipio.gov.br | (00) 0000-0000', 'arquivo-historico' ); ?></p>
		</div>
		<nav aria-label="<?php esc_attr_e( 'Menu de rodapé', 'arquivo-historico' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'rodape',
					'fallback_cb'    => false,
				)
			);
			?>
			<div class="accessibility-links">
				<a href="#conteudo-principal"><?php esc_html_e( 'Voltar ao conteúdo', 'arquivo-historico' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/acessibilidade' ) ); ?>"><?php esc_html_e( 'Acessibilidade', 'arquivo-historico' ); ?></a>
			</div>
		</nav>
	</div>
	<?php wp_footer(); ?>
</footer>
</body>
</html>
