<?php
/**
 * Consultation request page.
 *
 * The form posts to POST /wp-json/hhd/v1/consultation and is stored in the
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
		'eyebrow' => 'شروع یک پروژه',
		'title'   => 'درخواست مشاوره',
		'text'    => 'در یک جلسه‌ی اولیه، فضا، نیازها و محدودیت‌های پروژه بررسی می‌شود و مسیر پیشنهادی برای ادامه‌ی کار مشخص می‌گردد.',
		'image'   => 'hero-interior',
		'trail'   => array(
			array( 'label' => 'درخواست مشاوره' ),
		),
	)
);
?>

<section class="section">
	<div class="container container--wide">
		<?php
		echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'eyebrow' => 'چه اتفاقی می‌افتد؟',
				'title'   => 'مسیر مشاوره در سه گام',
			)
		);
		?>
		<div class="consultation-steps" data-stagger="110">
			<article class="principle">
				<span class="principle__number" dir="ltr">۰۱</span>
				<h3 class="principle__title">ثبت درخواست</h3>
				<p class="principle__text">فرم را با اطلاعات اولیه‌ی پروژه پر می‌کنید؛ نوع فضا، متراژ تقریبی و زمان‌بندی مورد نظرتان.</p>
			</article>
			<article class="principle">
				<span class="principle__number" dir="ltr">۰۲</span>
				<h3 class="principle__title">جلسه‌ی شناخت</h3>
				<p class="principle__text">در جلسه‌ی اولیه درباره‌ی سبک زندگی، انتظارات و محدودیت‌های فضا گفت‌وگو می‌شود.</p>
			</article>
			<article class="principle">
				<span class="principle__number" dir="ltr">۰۳</span>
				<h3 class="principle__title">پیشنهاد و برآورد</h3>
				<p class="principle__text">دامنه‌ی خدمات، زمان‌بندی و برآورد اولیه‌ی هزینه به‌صورت شفاف ارائه می‌گردد.</p>
			</article>
		</div>
	</div>
</section>

<section class="section section--flush-top">
	<div class="container container--wide">
		<div class="contact-layout">
			<div class="contact-layout__form">
				<?php
				echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'eyebrow' => 'فرم درخواست',
						'title'   => 'اطلاعات پروژه را بنویسید',
						'text'    => 'هرچه توضیحات دقیق‌تر باشد، جلسه‌ی اولیه مفیدتر خواهد بود.',
					)
				);
				?>

				<form class="form" data-consultation-form novalidate>
					<div class="form__row">
						<div class="field">
							<label class="field__label" for="consult-name">نام و نام خانوادگی</label>
							<input class="field__input" type="text" id="consult-name" name="consult-name" autocomplete="name" required>
						</div>

						<div class="field">
							<label class="field__label" for="consult-phone">شماره تماس</label>
							<input class="field__input" type="tel" id="consult-phone" name="consult-phone" autocomplete="tel" inputmode="tel" dir="ltr" required>
						</div>
					</div>

					<div class="form__row">
						<div class="field">
							<label class="field__label" for="consult-type">نوع پروژه</label>
							<select class="field__input field__select" id="consult-type" name="consult-type">
								<option value="">انتخاب کنید</option>
								<?php foreach ( hhd_project_types() as $hhd_type ) : ?>
									<option value="<?php echo esc_attr( $hhd_type ); ?>"><?php echo esc_html( $hhd_type ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="field">
							<label class="field__label" for="consult-area">متراژ تقریبی</label>
							<input class="field__input" type="text" id="consult-area" name="consult-area" inputmode="numeric" placeholder="مثلاً ۱۴۰ متر">
						</div>
					</div>

					<div class="form__row">
						<div class="field">
							<label class="field__label" for="consult-city">شهر</label>
							<input class="field__input" type="text" id="consult-city" name="consult-city" autocomplete="address-level2">
						</div>

						<div class="field">
							<label class="field__label" for="consult-time">زمان مناسب برای تماس</label>
							<select class="field__input field__select" id="consult-time" name="consult-time">
								<option value="">انتخاب کنید</option>
								<option value="صبح">صبح</option>
								<option value="بعدازظهر">بعدازظهر</option>
								<option value="عصر">عصر</option>
							</select>
						</div>
					</div>

					<div class="field">
						<label class="field__label" for="consult-message">توضیحات پروژه</label>
						<textarea class="field__input field__textarea" id="consult-message" name="consult-message" rows="6" placeholder="درباره‌ی فضا، سبک مورد علاقه، بودجه‌ی تقریبی و زمان‌بندی مورد نظرتان بنویسید."></textarea>
					</div>

					<div class="form__actions">
						<button class="btn btn--solid" type="submit">
							<span class="btn__label">ارسال درخواست</span>
							<span class="btn__icon"><?php echo hhd_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</button>
						<p class="form__note">اطلاعات پروژه فقط برای هماهنگی جلسه‌ی مشاوره استفاده می‌شود و محرمانه می‌ماند.</p>
					</div>
				</form>
			</div>

			<aside class="contact-aside">
				<?php echo hhd_eyebrow( 'راه‌های دیگر' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p>اگر ترجیح می‌دهید مستقیم صحبت کنید، از راه‌های زیر در دسترس هستیم.</p>

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
						<span class="contact-item__label">ساعات کاری</span>
						<span class="contact-item__value"><?php echo esc_html( $hhd_contact['hours'] ); ?></span>
						<span class="contact-item__note"><?php echo esc_html( $hhd_contact['hours_note'] ); ?></span>
					</div>
				</div>
			</aside>
		</div>
	</div>
</section>

<section class="section section--surface">
	<div class="container container--narrow">
		<?php
		echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'eyebrow' => 'پرسش‌های متداول',
				'title'   => 'پیش از ارسال درخواست',
			)
		);
		?>
		<div class="accordion">
			<?php foreach ( hhd_faqs() as $hhd_index => $hhd_faq ) : ?>
				<?php $hhd_panel_id = 'faq-consult-' . $hhd_index; ?>
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

<?php get_footer(); ?>
