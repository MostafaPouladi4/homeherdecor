<?php
/**
 * Design process — horizontal on desktop, a vertical timeline on mobile.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_steps = hhd_process_steps();
?>
<section class="section" id="process">
	<div class="container container--wide">
		<?php
		echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'eyebrow' => 'فرآیند',
				'title'   => 'مسیر طراحی یک فضا',
				'text'    => 'هر پروژه از یک گفت‌وگو شروع می‌شود و با تحویل فضایی آماده‌ی زندگی پایان می‌یابد. در تمام مسیر، تصمیم‌ها با شما گرفته می‌شوند.',
				'class'   => 'section-head--process',
			)
		);
		?>

		<ol class="process__track" data-stagger="80">
			<?php foreach ( $hhd_steps as $hhd_step ) : ?>
				<li class="process-step reveal">
					<span class="process-step__line" aria-hidden="true"></span>
					<span class="process-step__marker" aria-hidden="true"></span>
					<span class="process-step__number" dir="ltr"><?php echo esc_html( $hhd_step['number'] ); ?></span>
					<h3 class="process-step__title"><?php echo esc_html( $hhd_step['title'] ); ?></h3>
					<p class="process-step__text"><?php echo esc_html( $hhd_step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
