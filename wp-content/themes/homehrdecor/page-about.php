<?php
/**
 * About page.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$GLOBALS['hhd_header_solid'] = true;

get_header();

$hhd_contact = hhd_contact();

hhd_page_hero(
	array(
		'eyebrow' => 'درباره استودیو',
		'title'   => 'ما فضا را برای زندگی طراحی می‌کنیم',
		'text'    => 'homehrdecor یک استودیو طراحی داخلی است؛ جایی که زیبایی‌شناسی، عملکرد و جزئیات اجرایی به‌عنوان یک کل واحد دیده می‌شوند.',
		'image'   => 'city-tehran',
		'trail'   => array(
			array( 'label' => 'درباره ما' ),
		),
	)
);
?>

<section class="section section--lg">
	<div class="container container--wide">
		<div class="intro-grid">
			<div class="intro-grid__media reveal-media">
				<div class="media media--portrait">
					<?php
					echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'proj-dining',
						'فضایی مسکونی با ترکیب چوب و سنگ، نمونه‌ای از رویکرد استودیو در طراحی داخلی',
						array( 'sizes' => '(max-width: 1080px) 100vw, 45vw' )
					);
					?>
				</div>
				<div class="intro-grid__aside">
					<?php
					echo hhd_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'proj-detail',
						'جزئیات اجرایی کابینت با نور زیرکابینتی',
						array( 'sizes' => '240px' )
					);
					?>
				</div>
			</div>

			<div class="intro-grid__body">
				<?php
				echo hhd_eyebrow( 'رویکرد ما' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
				<h2 class="reveal">طراحی خوب، از یک گفت‌وگوی دقیق شروع می‌شود.</h2>
				<p class="lead reveal" style="--reveal-delay:90ms">
					هر پروژه با شناخت آغاز می‌شود: شناخت ساکنان، عادت‌های روزمره، محدودیت‌های فضا و انتظاراتی که همیشه به‌صراحت بیان نمی‌شوند. طراحی زمانی موفق است که این لایه‌های پنهان را به زبان فضا ترجمه کند.
				</p>
				<p class="reveal" style="--reveal-delay:130ms">
					ما به مینیمالیسم به‌عنوان یک سبک نگاه نمی‌کنیم؛ آن را راهی برای رسیدن به وضوح می‌دانیم. حذف آنچه لازم نیست، تا آنچه لازم است دیده شود.
				</p>

				<div class="intro-grid__signature reveal" style="--reveal-delay:180ms">
					<img src="<?php echo esc_url( hhd_brand_uri( 'homehrdecor-mark-128.png' ) ); ?>" width="56" height="56" alt="" aria-hidden="true" loading="lazy" decoding="async">
					<span class="intro-grid__signature-text">
						<span class="intro-grid__signature-name">محمد مهدی رنجبران</span>
						<span class="intro-grid__signature-role">طراح داخلی و مدیر خلاق homehrdecor</span>
					</span>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="section section--dark">
	<?php echo hhd_watermark( array( 'class' => 'watermark--philosophy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="container container--wide">
		<?php
		echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'eyebrow' => 'اصول کار',
				'title'   => 'سه اصلی که در هر پروژه تکرار می‌شود',
			)
		);
		?>
		<div class="principles" data-stagger="120">
			<?php foreach ( hhd_principles() as $hhd_principle ) : ?>
				<article class="principle">
					<span class="principle__number" dir="ltr"><?php echo esc_html( $hhd_principle['number'] ); ?></span>
					<h3 class="principle__title"><?php echo esc_html( $hhd_principle['title'] ); ?></h3>
					<p class="principle__text"><?php echo esc_html( $hhd_principle['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="section">
	<div class="container container--wide">
		<div class="detail-body">
			<aside class="detail-body__aside">
				<?php echo hhd_eyebrow( 'در یک نگاه' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="detail-block__text">این اعداد نمونه‌ی چیدمانی این بخش هستند و به‌عنوان آمار رسمی استودیو ارائه نمی‌شوند.</p>
			</aside>

			<div class="detail-body__content">
				<div class="stats">
					<?php foreach ( hhd_stats() as $hhd_stat ) : ?>
						<div class="stat">
							<p class="stat__value">
								<span data-count-to="<?php echo esc_attr( $hhd_stat['value'] ); ?>" dir="ltr"><?php echo esc_html( hhd_fa_num( $hhd_stat['value'] ) ); ?></span>
								<?php if ( ! empty( $hhd_stat['plus'] ) ) : ?>
									<span class="stat__plus" aria-hidden="true">+</span>
								<?php endif; ?>
							</p>
							<p class="stat__label"><?php echo esc_html( $hhd_stat['label'] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<section class="section section--surface">
	<div class="container container--wide">
		<?php get_template_part( 'template-parts/sections/process' ); ?>
	</div>
</section>

<section class="section">
	<div class="container container--wide">
		<div class="contact-layout">
			<div>
				<?php
				echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'eyebrow' => 'حوزه فعالیت',
						'title'   => 'خدماتی که ارائه می‌دهیم',
					)
				);
				?>
				<ul class="link-list">
					<?php foreach ( hhd_services() as $hhd_service ) : ?>
						<li>
							<a class="link-arrow" href="<?php echo esc_url( hhd_service_url( $hhd_service['slug'] ) ); ?>">
								<span><?php echo esc_html( $hhd_service['title'] ); ?></span>
								<?php echo hhd_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>

			<address class="contact-list">
				<div class="contact-item">
					<span class="contact-item__label">تلفن</span>
					<a class="contact-item__value" href="tel:<?php echo esc_attr( $hhd_contact['phone_href'] ); ?>" dir="ltr"><?php echo esc_html( $hhd_contact['phone_label'] ); ?></a>
				</div>
				<div class="contact-item">
					<span class="contact-item__label">ایمیل</span>
					<a class="contact-item__value" href="mailto:<?php echo esc_attr( $hhd_contact['email'] ); ?>" dir="ltr"><?php echo esc_html( $hhd_contact['email'] ); ?></a>
				</div>
				<div class="contact-item">
					<span class="contact-item__label">نشانی</span>
					<span class="contact-item__value"><?php echo esc_html( $hhd_contact['address'] ); ?></span>
				</div>
				<div class="contact-item">
					<span class="contact-item__label">ساعات کاری</span>
					<span class="contact-item__value"><?php echo esc_html( $hhd_contact['hours'] ); ?></span>
				</div>
			</address>
		</div>
	</div>
</section>

<section class="section section--flush-top">
	<div class="container container--wide">
		<?php
		echo hhd_section_head( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'eyebrow' => 'نمونه کارها',
				'title'   => 'چند پروژه از استودیو',
			)
		);
		?>
		<div class="related-projects__grid">
			<?php foreach ( hhd_projects( array( 'khane-aram', 'apartment-12', 'suite-wood' ) ) as $hhd_project ) : ?>
				<?php hhd_project_card( $hhd_project, array( 'layout' => 'portrait', 'sizes' => '(max-width: 720px) 100vw, 30vw' ) ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php
hhd_cta(
	array(
		'eyebrow' => 'شروع یک پروژه',
		'title'   => 'درباره پروژه‌ی خودتان صحبت کنیم.',
		'text'    => 'اگر می‌خواهید بدانید فضای شما چه ظرفیتی دارد، درخواست مشاوره بفرستید.',
	)
);

get_footer();
