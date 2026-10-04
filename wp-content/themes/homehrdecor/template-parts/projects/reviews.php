<?php
/**
 * Project reviews.
 *
 * Published reviews for the current project, plus a submission form. Only
 * `approved` reviews are ever read here — moderation happens in the
 * dashboard, and nothing a visitor submits appears until an admin approves
 * it.
 *
 * The target is resolved through hhd_review_target_ref(), so this template
 * never touches the slug column itself.
 *
 * Deliberately not wrapped in .reveal-media: that class clips its observed
 * element, and a clipped element reports an IntersectionObserver ratio of
 * zero forever, so the block would never reveal. .reveal (opacity) is safe.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_review_project = isset( $args['project'] ) ? $args['project'] : hhd_get_project( hhd_current_project_slug() );

if ( ! $hhd_review_project ) {
	return;
}

$hhd_review_ref = hhd_review_target_ref( $hhd_review_project );

if ( '' === $hhd_review_ref ) {
	return;
}

$hhd_reviews = hhd_approved_reviews_for_target( $hhd_review_ref, 12 );
$hhd_summary = hhd_review_summary_for_target( $hhd_review_ref );
?>
<section class="section section--surface" id="reviews">
	<div class="container container--wide">
		<?php
		echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'eyebrow' => 'تجربه‌ی کارفرما',
				'title'   => 'نظرها درباره‌ی این پروژه',
				'text'    => $hhd_summary['count']
					? 'نظرهای ثبت‌شده توسط کارفرمایان این پروژه. هر نظر پیش از انتشار بررسی می‌شود.'
					: 'هنوز نظری برای این پروژه ثبت نشده است. اگر با ما کار کرده‌اید، اولین نفر باشید.',
			)
		);
		?>

		<div class="reviews-layout">
			<div class="reviews-layout__list">
				<?php if ( $hhd_summary['count'] ) : ?>
					<div class="reviews-summary">
						<span class="reviews-summary__value num"><?php echo esc_html( hhd_fa_num( number_format( $hhd_summary['average'], 1 ) ) ); ?></span>
						<span class="reviews-summary__meta">
							<?php echo hhd_rating_stars( $hhd_summary['average'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="reviews-summary__count">
								<?php
								printf(
									/* translators: %s: number of reviews */
									esc_html( 'از %s نظر' ),
									esc_html( hhd_fa_num( $hhd_summary['count'] ) )
								);
								?>
							</span>
						</span>
					</div>
				<?php endif; ?>

				<?php if ( $hhd_reviews ) : ?>
					<div class="reviews-list">
						<?php foreach ( $hhd_reviews as $hhd_review ) : ?>
							<article class="review">
								<header class="review__head">
									<span class="review__author"><?php echo esc_html( $hhd_review['name'] ); ?></span>
									<?php echo hhd_rating_stars( (int) $hhd_review['rating'], 'rating--sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</header>
								<p class="review__text"><?php echo esc_html( $hhd_review['message'] ); ?></p>
								<p class="review__date"><?php echo esc_html( hhd_date_label( $hhd_review['created_at'], false ) ); ?></p>
							</article>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="reviews-empty">هنوز نظر تأییدشده‌ای برای این پروژه ثبت نشده است.</p>
				<?php endif; ?>
			</div>

			<div class="reviews-layout__form">
				<?php echo hhd_eyebrow( 'ثبت نظر' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

				<form class="form form--stack" data-review-form novalidate>
					<input type="hidden" name="review-project" value="<?php echo esc_attr( $hhd_review_ref ); ?>">

					<div class="field">
						<label class="field__label" for="review-name">نام شما</label>
						<input class="field__input" type="text" id="review-name" name="review-name" autocomplete="name" required>
					</div>

					<div class="field">
						<label class="field__label" for="review-rating">امتیاز</label>
						<select class="field__input field__select" id="review-rating" name="review-rating" required>
							<option value="">انتخاب کنید</option>
							<option value="5">۵ — عالی</option>
							<option value="4">۴ — خوب</option>
							<option value="3">۳ — قابل قبول</option>
							<option value="2">۲ — ضعیف</option>
							<option value="1">۱ — نامناسب</option>
						</select>
					</div>

					<div class="field">
						<label class="field__label" for="review-message">متن نظر</label>
						<textarea class="field__input field__textarea" id="review-message" name="review-message" rows="5" placeholder="از تجربه‌ی همکاری با ما بنویسید." required></textarea>
					</div>

					<div class="form__actions">
						<button class="btn btn--solid" type="submit">
							<span class="btn__label">ثبت نظر</span>
							<span class="btn__icon"><?php echo hhd_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</button>
						<p class="form__note">نظر شما پس از بررسی و تأیید در همین صفحه منتشر می‌شود.</p>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
