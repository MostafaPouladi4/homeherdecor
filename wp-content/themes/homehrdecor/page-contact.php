<?php
/**
 * Contact page.
 *
 * The form posts to POST /wp-json/hhd/v1/contact and is stored in the
 * theme's requests table. The markup here is the presentation; the
 * submission itself is handled in assets/js/main.js.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['hhd_header_solid'] = true;

get_header();

$hhd_contact = hhd_contact();

hhd_page_hero(
	array(
		'eyebrow' => 'در تماس باشیم',
		'title'   => 'تماس با ما',
		'text'    => 'برای گفت‌وگو درباره‌ی پروژه، بازدید از استودیو یا هر پرسشی درباره‌ی خدمات، از راه‌های زیر با ما در ارتباط باشید.',
		'image'   => 'proj-facade-night',
		'trail'   => array(
			array( 'label' => 'تماس با ما' ),
		),
	)
);
?>

<section class="section">
	<div class="container container--wide">
		<div class="contact-layout">
			<div class="contact-layout__info">
				<?php
				echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'eyebrow' => 'اطلاعات تماس',
						'title'   => 'می‌توانید مستقیم تماس بگیرید',
						'text'    => 'پاسخ‌گویی در ساعات کاری است؛ پیام‌های خارج از این بازه، روز کاری بعد بررسی می‌شوند.',
					)
				);
				?>

				<div class="contact-list">
					<div class="contact-item">
						<span class="contact-item__label">تلفن</span>
						<a class="contact-item__value" href="tel:<?php echo esc_attr( $hhd_contact['phone_href'] ); ?>" dir="ltr"><?php echo esc_html( $hhd_contact['phone_label'] ); ?></a>
					</div>
					<div class="contact-item">
						<span class="contact-item__label">ایمیل</span>
						<a class="contact-item__value" href="mailto:<?php echo esc_attr( $hhd_contact['email'] ); ?>" dir="ltr"><?php echo esc_html( $hhd_contact['email'] ); ?></a>
					</div>
					<div class="contact-item">
						<span class="contact-item__label">اینستاگرام</span>
						<a class="contact-item__value" href="<?php echo esc_url( 'https://instagram.com/' . $hhd_contact['instagram'] ); ?>" rel="noopener noreferrer" target="_blank" dir="ltr"><?php echo esc_html( $hhd_contact['instagram'] ); ?></a>
					</div>
					<div class="contact-item">
						<span class="contact-item__label">نشانی استودیو</span>
						<span class="contact-item__value"><?php echo esc_html( $hhd_contact['address'] ); ?></span>
						<span class="contact-item__note"><?php echo esc_html( $hhd_contact['address_note'] ); ?></span>
					</div>
					<div class="contact-item">
						<span class="contact-item__label">ساعات کاری</span>
						<span class="contact-item__value"><?php echo esc_html( $hhd_contact['hours'] ); ?></span>
						<span class="contact-item__note"><?php echo esc_html( $hhd_contact['hours_note'] ); ?></span>
					</div>
				</div>
			</div>

			<div class="contact-layout__form">
				<?php
				echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'eyebrow' => 'فرم تماس',
						'title'   => 'پیام خود را بنویسید',
					)
				);
				?>

				<form class="form" data-contact-form novalidate>
					<div class="form__row">
						<div class="field">
							<label class="field__label" for="contact-name">نام و نام خانوادگی</label>
							<input class="field__input" type="text" id="contact-name" name="contact-name" autocomplete="name" required>
						</div>

						<div class="field">
							<label class="field__label" for="contact-phone">شماره تماس</label>
							<input class="field__input" type="tel" id="contact-phone" name="contact-phone" autocomplete="tel" inputmode="tel" dir="ltr" required>
						</div>
					</div>

					<div class="form__row">
						<div class="field">
							<label class="field__label" for="contact-type">نوع پروژه</label>
							<select class="field__input field__select" id="contact-type" name="contact-type">
								<option value="">انتخاب کنید</option>
								<?php foreach ( hhd_project_types() as $hhd_type ) : ?>
									<option value="<?php echo esc_attr( $hhd_type ); ?>"><?php echo esc_html( $hhd_type ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="field">
							<label class="field__label" for="contact-area">متراژ تقریبی</label>
							<input class="field__input" type="text" id="contact-area" name="contact-area" inputmode="numeric" placeholder="مثلاً ۱۲۰ متر">
						</div>
					</div>

					<div class="field">
						<label class="field__label" for="contact-city">شهر</label>
						<input class="field__input" type="text" id="contact-city" name="contact-city" autocomplete="address-level2">
					</div>

					<div class="field">
						<label class="field__label" for="contact-message">توضیحات</label>
						<textarea class="field__input field__textarea" id="contact-message" name="contact-message" rows="5" placeholder="کمی درباره‌ی فضا، نیازها و زمان‌بندی مورد نظرتان بنویسید."></textarea>
					</div>

					<div class="form__actions">
						<button class="btn btn--solid" type="submit">
							<span class="btn__label">ارسال پیام</span>
							<span class="btn__icon"><?php echo hhd_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</button>
						<p class="form__note">اطلاعات شما فقط برای پاسخ‌گویی به همین درخواست استفاده می‌شود و در اختیار کسی قرار نمی‌گیرد.</p>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<section class="section section--surface">
	<div class="container container--wide">
		<?php
		echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'eyebrow' => 'پرسش‌های متداول',
				'title'   => 'شاید پاسخ سوال شما اینجا باشد',
			)
		);
		?>
		<div class="accordion">
			<?php foreach ( hhd_faqs() as $hhd_index => $hhd_faq ) : ?>
				<?php $hhd_panel_id = 'faq-contact-' . $hhd_index; ?>
				<div class="accordion__item">
					<h3>
						<button class="accordion__trigger" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $hhd_panel_id ); ?>">
							<span><?php echo esc_html( $hhd_faq['q'] ); ?></span>
							<span class="accordion__icon" aria-hidden="true"></span>
						</button>
					</h3>
					<div class="accordion__panel" id="<?php echo esc_attr( $hhd_panel_id ); ?>">
						<div class="accordion__panel-inner"><?php echo esc_html( $hhd_faq['a'] ); ?></div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
hhd_cta(
	array(
		'eyebrow' => 'آماده‌اید؟',
		'title'   => 'برای شروع، یک جلسه کافی است.',
		'text'    => 'فرم درخواست مشاوره را پر کنید تا زمان جلسه‌ی اولیه هماهنگ شود.',
	)
);

get_footer();
