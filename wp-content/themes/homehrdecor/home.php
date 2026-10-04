<?php
/**
 * Journal archive — the posts page.
 *
 * Falls back to the demo article set while the site has no published
 * posts, so the layout can be reviewed before content exists.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['hhd_header_solid'] = true;

get_header();

hhd_page_hero(
	array(
		'eyebrow' => 'مجله homehrdecor',
		'title'   => 'یادداشت‌ها و راهنماها',
		'text'    => 'نوشته‌هایی درباره‌ی طراحی داخلی، انتخاب متریال، بازسازی و تصمیم‌هایی که کیفیت زندگی در فضا را تغییر می‌دهند.',
		'image'   => 'proj-bedroom',
		'trail'   => array(
			array( 'label' => 'مجله' ),
		),
	)
);

if ( have_posts() ) :
	$hhd_first = true;
	$hhd_cards = array();
	?>
	<section class="section">
		<div class="container container--wide">
			<?php
			while ( have_posts() ) :
				the_post();

				if ( $hhd_first ) :
					$hhd_first = false;
					?>
					<article class="journal-featured">
						<div class="journal-featured__media media media--landscape reveal-media">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'large', array( 'loading' => 'eager' ) );
							}
							?>
						</div>
						<div>
							<p class="article-meta">
								<?php
								$hhd_cats = get_the_category();
								if ( $hhd_cats ) :
									?>
									<span><?php echo esc_html( $hhd_cats[0]->name ); ?></span>
								<?php endif; ?>
								<span aria-hidden="true">—</span>
								<span><?php echo esc_html( hhd_fa_num( get_the_date() ) ); ?></span>
							</p>
							<h2 class="reveal"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="lead"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
							<?php
							echo hhd_link( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								array(
									'label' => 'خواندن مقاله',
									'url'   => get_permalink(),
								)
							);
							?>
						</div>
					</article>
					<?php
				else :
					$hhd_post_cats = get_the_category();
					$hhd_cards[]   = array(
						'title'    => get_the_title(),
						'url'      => get_permalink(),
						'category' => $hhd_post_cats ? $hhd_post_cats[0]->name : 'مقاله',
						'date'     => hhd_fa_num( get_the_date() ),
						'excerpt'  => wp_trim_words( get_the_excerpt(), 24 ),
						'image'    => 'proj-detail',
					);
				endif;
			endwhile;
			?>

			<?php if ( ! empty( $hhd_cards ) ) : ?>
				<div class="journal-grid" style="margin-block-start:var(--space-2xl)">
					<?php foreach ( $hhd_cards as $hhd_card ) : ?>
						<?php hhd_journal_card( $hhd_card ); ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'class'     => 'pagination',
					'prev_text' => 'جدیدتر',
					'next_text' => 'قدیمی‌تر',
				)
			);
			?>
		</div>
	</section>
	<?php
else :
	$hhd_demo = hhd_journal_fallback();
	?>
	<section class="section">
		<div class="container container--wide">
			<article class="journal-featured">
				<div class="journal-featured__media media media--landscape reveal-media">
					<?php
					echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$hhd_demo[0]['image'],
						$hhd_demo[0]['title'],
						array(
							'sizes'   => '(max-width: 1080px) 100vw, 55vw',
							'loading' => 'eager',
						)
					);
					?>
				</div>
				<div>
					<p class="article-meta">
						<span><?php echo esc_html( $hhd_demo[0]['category'] ); ?></span>
						<span aria-hidden="true">—</span>
						<span><?php echo esc_html( $hhd_demo[0]['date'] ); ?></span>
					</p>
					<h2><?php echo esc_html( $hhd_demo[0]['title'] ); ?></h2>
					<p class="lead"><?php echo esc_html( $hhd_demo[0]['excerpt'] ); ?></p>
					<p class="form__note">این مقاله نمونه‌ی چیدمانی است؛ محتوای واقعی پس از انتشار نوشته‌ها نمایش داده می‌شود.</p>
				</div>
			</article>

			<div class="journal-grid">
				<?php foreach ( array_slice( $hhd_demo, 1 ) as $hhd_card ) : ?>
					<?php hhd_journal_card( $hhd_card ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
endif;

hhd_cta(
	array(
		'eyebrow' => 'شروع یک پروژه',
		'title'   => 'سوالی دارید که در مقالات پیدا نکردید؟',
		'text'    => 'برای پرسش‌های مربوط به پروژه‌ی خودتان، درخواست مشاوره بفرستید.',
	)
);

get_footer();
