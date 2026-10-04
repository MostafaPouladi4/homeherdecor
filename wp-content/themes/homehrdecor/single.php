<?php
/**
 * Single journal article.
 *
 * An editorial reading layout: narrow measure, generous leading, no
 * sidebar furniture competing with the text.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['hhd_header_solid'] = true;

get_header();

while ( have_posts() ) :
	the_post();

	$hhd_categories = get_the_category();
	$hhd_category   = $hhd_categories ? $hhd_categories[0]->name : 'مقاله';
	$hhd_related    = get_posts(
		array(
			'post__not_in'   => array( get_the_ID() ),
			'posts_per_page' => 3,
			'category__in'   => $hhd_categories ? wp_list_pluck( $hhd_categories, 'term_id' ) : array(),
			'orderby'        => 'date',
		)
	);

	if ( ! $hhd_related ) {
		$hhd_related = get_posts(
			array(
				'post__not_in'   => array( get_the_ID() ),
				'posts_per_page' => 3,
			)
		);
	}
	?>

	<article <?php post_class( 'entry entry--article' ); ?>>
		<?php
		hhd_page_hero(
			array(
				'eyebrow' => $hhd_category,
				'title'   => get_the_title(),
				'trail'   => array(
					array(
						'label' => 'مجله',
						'url'   => hhd_page_url( 'journal' ),
					),
					array( 'label' => get_the_title() ),
				),
			)
		);
		?>

		<div class="container container--narrow">
			<p class="article-meta">
				<span><?php echo esc_html( $hhd_category ); ?></span>
				<span aria-hidden="true">—</span>
				<span><?php echo esc_html( hhd_fa_num( get_the_date() ) ); ?></span>
				<?php if ( get_the_author() ) : ?>
					<span aria-hidden="true">—</span>
					<span><?php echo esc_html( get_the_author() ); ?></span>
				<?php endif; ?>
			</p>
		</div>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="container container--wide">
				<div class="media media--landscape reveal-media">
					<?php the_post_thumbnail( 'full', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
				</div>
			</figure>
		<?php endif; ?>

		<section class="section">
			<div class="container container--narrow">
				<div class="article-body">
					<?php the_content(); ?>

					<?php
					wp_link_pages(
						array(
							'before' => '<nav class="pagination">',
							'after'  => '</nav>',
						)
					);
					?>
				</div>

				<?php if ( has_tag() ) : ?>
					<div class="materials-list" style="margin-block-start:var(--space-xl)">
						<?php foreach ( get_the_tags() as $hhd_tag ) : ?>
							<a class="material-tag" href="<?php echo esc_url( get_tag_link( $hhd_tag->term_id ) ); ?>"><?php echo esc_html( $hhd_tag->name ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<nav class="post-nav" aria-label="<?php esc_attr_e( 'پیمایش نوشته‌ها', 'homehrdecor' ); ?>">
					<?php
					$hhd_prev = get_previous_post();
					$hhd_next = get_next_post();
					?>
					<?php if ( $hhd_prev ) : ?>
						<a class="post-nav__item" href="<?php echo esc_url( get_permalink( $hhd_prev ) ); ?>">
							<span class="post-nav__label">نوشته‌ی قبلی</span>
							<span class="post-nav__title"><?php echo esc_html( get_the_title( $hhd_prev ) ); ?></span>
						</a>
					<?php endif; ?>
					<?php if ( $hhd_next ) : ?>
						<a class="post-nav__item post-nav__item--next" href="<?php echo esc_url( get_permalink( $hhd_next ) ); ?>">
							<span class="post-nav__label">نوشته‌ی بعدی</span>
							<span class="post-nav__title"><?php echo esc_html( get_the_title( $hhd_next ) ); ?></span>
						</a>
					<?php endif; ?>
				</nav>
			</div>
		</section>

		<?php if ( $hhd_related ) : ?>
			<section class="section section--surface">
				<div class="container container--wide">
					<?php
					echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'eyebrow' => 'ادامه بدهید',
							'title'   => 'نوشته‌های مرتبط',
						)
					);
					?>
					<div class="journal-grid">
						<?php
						$hhd_fallbacks = array( 'proj-detail', 'proj-dining', 'proj-bedroom' );
						foreach ( $hhd_related as $hhd_offset => $hhd_post ) :
							$hhd_post_cats = get_the_category( $hhd_post->ID );
							hhd_journal_card(
								array(
									'title'    => get_the_title( $hhd_post ),
									'url'      => get_permalink( $hhd_post ),
									'category' => $hhd_post_cats ? $hhd_post_cats[0]->name : 'مقاله',
									'date'     => hhd_fa_num( get_the_date( '', $hhd_post ) ),
									'excerpt'  => wp_trim_words( get_the_excerpt( $hhd_post ), 22 ),
									'image'    => $hhd_fallbacks[ $hhd_offset % count( $hhd_fallbacks ) ],
								)
							);
						endforeach;
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</article>

	<?php
endwhile;

hhd_cta(
	array(
		'eyebrow' => 'شروع یک پروژه',
		'title'   => 'درباره‌ی فضای خودتان صحبت کنیم.',
		'text'    => 'اگر می‌خواهید بدانید این ایده‌ها چطور در فضای شما اجرا می‌شود، درخواست مشاوره بفرستید.',
	)
);

get_footer();
