<?php
/**
 * Testimonials — placeholder quotes for the demo layout.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_testimonials = hhd_testimonials();
?>
<section class="section" id="testimonials">
	<div class="container container--wide">
		<?php
		echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'eyebrow' => 'تجربه کارفرما',
				'title'   => 'آن‌چه درباره همکاری گفته می‌شود',
				'text'    => 'چند نمونه از بازخوردهای کارفرمایان پروژه‌های پیشین.',
			)
		);
		?>

		<div class="testimonials" data-stagger="110">
			<?php foreach ( $hhd_testimonials as $hhd_item ) : ?>
				<figure class="testimonial reveal">
					<blockquote class="testimonial__quote">
						<?php echo esc_html( $hhd_item['quote'] ); ?>
					</blockquote>
					<figcaption class="testimonial__author">
						<span class="testimonial__name"><?php echo esc_html( $hhd_item['name'] ); ?></span>
						<span class="testimonial__project"><?php echo esc_html( $hhd_item['project'] ); ?></span>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
