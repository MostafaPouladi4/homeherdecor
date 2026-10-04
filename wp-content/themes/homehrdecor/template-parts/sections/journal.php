<?php
/**
 * Journal — a magazine-style teaser.
 *
 * Reads real posts when they exist and falls back to the demo articles
 * in inc/data.php so the layout is complete before the blog is written.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_posts = get_posts(
	array(
		'numberposts'      => 4,
		'post_status'      => 'publish',
		'suppress_filters' => false,
	)
);

$hhd_articles = array();

if ( $hhd_posts ) {
	foreach ( $hhd_posts as $hhd_post ) {
		$hhd_category = get_the_category( $hhd_post->ID );
		$hhd_articles[] = array(
			'category' => $hhd_category ? $hhd_category[0]->name : 'مقاله',
			'date'     => hhd_fa_num( get_the_date( 'Y/m/d', $hhd_post ) ),
			'title'    => get_the_title( $hhd_post ),
			'excerpt'  => wp_trim_words( get_the_excerpt( $hhd_post ), 22, '…' ),
			'url'      => get_permalink( $hhd_post ),
			'image'    => 'proj-dining',
		);
	}
} else {
	foreach ( hhd_journal_fallback() as $hhd_article ) {
		$hhd_articles[] = array(
			'category' => $hhd_article['category'],
			'date'     => $hhd_article['date'],
			'title'    => $hhd_article['title'],
			'excerpt'  => $hhd_article['excerpt'],
			'url'      => '#',
			'image'    => $hhd_article['image'],
		);
	}
}
?>
<section class="section section--surface" id="journal">
	<div class="container container--wide">
		<div class="archive-head">
			<?php
			echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'eyebrow' => 'مجله',
					'title'   => 'یادداشت‌ها و راهنماها',
					'text'    => 'نوشته‌هایی درباره طراحی داخلی، انتخاب متریال و تصمیم‌هایی که کیفیت یک فضا را تعیین می‌کنند.',
				)
			);
			?>
			<div class="reveal">
				<?php
				echo hhd_link( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'label' => 'همه مقالات',
						'url'   => hhd_page_url( 'journal' ),
					)
				);
				?>
			</div>
		</div>

		<div class="journal-grid" data-stagger="90">
			<?php foreach ( array_slice( $hhd_articles, 0, 3 ) as $hhd_article ) : ?>
				<div class="reveal">
					<?php hhd_journal_card( $hhd_article ); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
