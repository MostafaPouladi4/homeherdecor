<?php
/**
 * Generic page template — the fallback for any page without a
 * dedicated template.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['hhd_header_solid'] = true;

get_header();
?>

<?php
while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class( 'entry' ); ?>>
		<?php
		hhd_page_hero(
			array(
				'title' => get_the_title(),
				'trail' => array(
					array( 'label' => get_the_title() ),
				),
			)
		);
		?>

		<section class="section">
			<div class="container container--narrow">
				<div class="article-body">
					<?php the_content(); ?>
				</div>
			</div>
		</section>

		<?php if ( get_edit_post_link() ) : ?>
			<div class="container container--narrow">
				<p class="form__note"><?php edit_post_link( __( 'ویرایش این صفحه', 'homehrdecor' ) ); ?></p>
			</div>
		<?php endif; ?>
	</article>

	<?php
endwhile;

get_footer();
