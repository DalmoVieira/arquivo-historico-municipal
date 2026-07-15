<?php
/**
 * Template de página genérica.
 *
 * @package Arquivo_Historico
 */

get_header();
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</article>
		<?php
	endwhile;
endif;
get_footer();
