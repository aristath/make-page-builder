<?php
/**
 * Template Name: Builder Template
 *
 * @package Make
 */

get_header();
?>

<main id="site-main" class="site-main" role="main">
<?php if ( have_posts() ) : ?>

	<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
		<?php Make_PB::get_template_part( 'partials/content', 'page-builder' ); ?>
		<?php Make_PB::get_template_part( 'partials/content', 'comments' ); ?>
	<?php endwhile; ?>

<?php endif; ?>
</main>

<?php get_footer(); ?>
