<?php
/**
 * Design philosophy — a dark, immersive section carrying the oversized
 * brand watermark.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_principles = hhd_principles();
?>
<section class="section section--lg section--dark" id="philosophy">
	<?php echo hhd_watermark( array( 'class' => 'watermark--philosophy', 'variant' => 'knockout' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<div class="container container--wide">
		<?php
		echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'eyebrow' => 'نگاه ما',
				'title'   => 'طراحی از نگاه ما',
				'text'    => 'سه اصل ساده که مسیر هر پروژه را در استودیو تعیین می‌کنند.',
				'class'   => 'section-head--invert',
			)
		);
		?>

		<div class="principles" data-stagger="120">
			<?php foreach ( $hhd_principles as $hhd_principle ) : ?>
				<article class="principle reveal">
					<span class="principle__number" dir="ltr"><?php echo esc_html( $hhd_principle['number'] ); ?></span>
					<h3 class="principle__title"><?php echo esc_html( $hhd_principle['title'] ); ?></h3>
					<p class="principle__text"><?php echo esc_html( $hhd_principle['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
