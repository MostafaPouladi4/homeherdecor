<?php
/**
 * Static demo content.
 *
 * Every value rendered by the theme comes from these arrays so the
 * presentation layer never talks to the database directly. When real
 * custom post types arrive, only the accessor functions below change.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

/**
 * Projects.
 *
 * "image" refers to a slug inside assets/images/ that has a generated
 * responsive set (see inc/template-tags.php → hhd_img()).
 *
 * @return array<string, array<string, mixed>>
 */
function hhd_projects_data() {
	static $projects = null;
	if ( null !== $projects ) {
		return $projects;
	}

	$projects = array(
		'khane-aram'      => array(
			'number'    => '01',
			'title'     => 'خانه آرام',
			'category'  => 'residential',
			'location'  => 'تهران',
			'style'     => 'مدرن مینیمال',
			'year'      => '۱۴۰۵',
			'area'      => '۱۸۰ متر',
			'client'    => 'خانواده‌ای جوان',
			'image'     => 'proj-dining',
			'excerpt'   => 'بازطراحی یک واحد آپارتمانی با تمرکز بر نور طبیعی، خطوط ساده و جنس‌های گرم؛ فضایی که در آن سکوت بصری، آرامش را ممکن می‌کند.',
			'intro'     => 'خانه آرام از یک پرسش ساده شروع شد: چگونه می‌توان در یک آپارتمان شهری، حس یک خانه‌ی وسیع و آرام را ساخت؟ پاسخ در حذف کردن بود، نه اضافه کردن. دیوارهای غیرضروری برداشته شدند تا نور از دو جهت وارد شود و جریان فضا یکپارچه شود.',
			'challenge' => 'پلان اولیه به چند اتاق کوچک و بسته تقسیم شده بود. نور فقط از یک سمت می‌آمد و آشپزخانه در تاریک‌ترین بخش خانه قرار داشت. کارفرما از شلوغی بصری و رنگ‌های تند خسته بود.',
			'concept'   => 'کانسپت طراحی بر «امتداد» بنا شد؛ امتداد خطوط کابینت تا دیوار، امتداد جنس چوب تا کف، و امتداد نور تا عمق فضا. پالت رنگی از جنس چوب بلوط، سنگ روشن و فلز برنزی مات شکل گرفت.',
			'materials' => array( 'چوب بلوط طبیعی', 'سنگ مرمر روشن', 'برنز مات', 'گچ میکروبتن' ),
			'gallery'   => array( 'proj-dining', 'proj-kitchen-grey', 'proj-detail', 'proj-bedroom' ),
		),
		'villa-aftab'     => array(
			'number'    => '02',
			'title'     => 'ویلای آفتاب',
			'category'  => 'villa',
			'location'  => 'اصفهان',
			'style'     => 'مدرن لوکس',
			'year'      => '۱۴۰۴',
			'area'      => '۴۲۰ متر',
			'client'    => 'سفارش خصوصی',
			'image'     => 'proj-kitchen-dark',
			'excerpt'   => 'ویلایی با ارتفاع زیاد و پنجره‌های سراسری؛ طراحی داخلی بر پایه‌ی سنگ، چوب تیره و نورپردازی موضعی برای ساختن فضایی باشکوه اما گرم.',
			'intro'     => 'ویلای آفتاب روی قطعه‌ای مشرف به دشت بنا شده است. طراحی داخلی می‌بایست هم‌زمان با منظره‌ی بیرون رقابت نمی‌کرد و هم هویتی مستقل داشت. انتخاب ما، سکوت جنس‌ها بود تا چشم به بیرون هدایت شود.',
			'challenge' => 'ارتفاع زیاد سقف و سطح شیشه‌ی گسترده، مقیاس فضا را از مقیاس انسانی دور می‌کرد. بدون مداخله، فضا سرد و پرت به نظر می‌رسید.',
			'concept'   => 'با افزودن یک حجم مرکزی از سنگ تیره در قلب فضا، لنگر بصری ساخته شد. نورپردازی لایه‌ای — خطی، موضعی و شاخص — ارتفاع را به جای سرکوب، جشن می‌گیرد.',
			'materials' => array( 'سنگ کوارتزیت تیره', 'چوب گردو', 'فلز برنجی', 'شیشه سکوریت' ),
			'gallery'   => array( 'proj-kitchen-dark', 'proj-detail', 'proj-dining' ),
		),
		'apartment-12'    => array(
			'number'    => '03',
			'title'     => 'آپارتمان ۱۲',
			'category'  => 'residential',
			'location'  => 'تهران',
			'style'     => 'Contemporary',
			'year'      => '۱۴۰۴',
			'area'      => '۱۴۵ متر',
			'client'    => 'زوج جوان',
			'image'     => 'proj-kitchen-navy',
			'excerpt'   => 'ترکیب رنگ سرمه‌ای عمیق با سطوح براق و نور مخفی؛ پروژه‌ای برای کسانی که فضایی جسور، منظم و درخشان می‌خواهند.',
			'intro'     => 'آپارتمان ۱۲ برای زوجی طراحی شد که پیش از هر چیز، نظم و شفافیت بصری می‌خواستند. خطوط طراحی بسیار تمیز و کابینت‌ها بدون دستگیره‌ی نمایان اجرا شدند.',
			'challenge' => 'آشپزخانه‌ی باز در مرکز پلان قرار داشت و می‌بایست بدون دیوار، از فضای نشیمن تفکیک می‌شد؛ در عین حال نباید بسته و سنگین به نظر می‌رسید.',
			'concept'   => 'تفکیک با رنگ انجام شد نه با دیوار. سرمه‌ای عمیق به‌عنوان رنگ شاخص انتخاب شد و سطح براق، نور را در فضا پخش می‌کند و حجم را سبک‌تر نشان می‌دهد.',
			'materials' => array( 'ام‌دی‌اف براق سرمه‌ای', 'کوارتز سفید', 'نور خطی مخفی', 'یراقآلات برنجی' ),
			'gallery'   => array( 'proj-kitchen-navy', 'proj-detail', 'proj-kitchen-grey' ),
		),
		'cafe-art'        => array(
			'number'    => '04',
			'title'     => 'کافه آرت',
			'category'  => 'commercial',
			'location'  => 'اصفهان',
			'style'     => 'Industrial Modern',
			'year'      => '۱۴۰۴',
			'area'      => '۲۱۰ متر',
			'client'    => 'مجموعه‌ی کافه آرت',
			'image'     => 'proj-facade-night',
			'excerpt'   => 'بازآفرینی یک فضای تجاری با لایه‌های چوبی عمودی، نور گرم و ریتمی که از نمای شبانه تا داخل ادامه پیدا می‌کند.',
			'intro'     => 'کافه آرت در طبقه‌ی همکف یک ساختمان مسکونی قرار دارد. چالش اصلی، ساختن هویتی متمایز برای یک کسب‌وکار تازه، در عین احترام به بافت شهری اطراف بود.',
			'challenge' => 'نما می‌بایست هم شب و هم روز خوانا باشد و در عین حال نور مزاحم برای همسایه‌ها ایجاد نکند. داخل نیز به ارتفاع و نور کافی نیاز داشت.',
			'concept'   => 'پرده‌ای از تیغه‌های چوبی عمودی روی نما اجرا شد که هم نور را کنترل می‌کند و هم ریتمی معمارانه می‌سازد. همان ریتم در داخل، با فاصله‌گذاری متناسب تکرار می‌شود.',
			'materials' => array( 'تیغه چوبی ترمو', 'فلز سیاه مات', 'بتن اکسپوز', 'نور گرم ۲۷۰۰ کلوین' ),
			'gallery'   => array( 'proj-facade-night', 'proj-detail', 'proj-kitchen-dark' ),
		),
		'office-ava'      => array(
			'number'    => '05',
			'title'     => 'دفتر آوا',
			'category'  => 'office',
			'location'  => 'تهران',
			'style'     => 'مینیمال اداری',
			'year'      => '۱۴۰۳',
			'area'      => '۳۲۰ متر',
			'client'    => 'شرکت آوا',
			'image'     => 'proj-detail',
			'excerpt'   => 'فضای کاری‌ای بر پایه‌ی جزئیات دقیق؛ کشوها، خطوط نور و سطوح مات در خدمت تمرکز و کارایی روزانه.',
			'intro'     => 'دفتر آوا برای تیمی طراحی شد که بخش زیادی از روز را در فضای مشترک می‌گذراند. هدف، ساختن محیطی آرام و کم‌ادعا بود که در آن جزئیات، کیفیت را منتقل کنند.',
			'challenge' => 'فضای باز اداری به‌سرعت شلوغ و پرصدا می‌شود. از سوی دیگر، کارفرما نورپردازی تند و رسمی اداری را نمی‌خواست.',
			'concept'   => 'جزئیات به‌جای تزئین: خط نور زیر کابینت‌ها، دستگیره‌های هم‌تراز و کشوهایی که بی‌صدا بسته می‌شوند. کف و دیوار با پالت محدود، پس‌زمینه‌ای آرام برای تمرکز می‌سازند.',
			'materials' => array( 'روکش پلی‌اورتان مات', 'فلز آنادایز', 'نور خطی', 'کف وینیل آکوستیک' ),
			'gallery'   => array( 'proj-detail', 'proj-kitchen-grey', 'proj-dining' ),
		),
		'suite-wood'      => array(
			'number'    => '06',
			'title'     => 'سوئیت چوب',
			'category'  => 'residential',
			'location'  => 'کرج',
			'style'     => 'مینیمال گرم',
			'year'      => '۱۴۰۳',
			'area'      => '۹۵ متر',
			'client'    => 'سفارش خصوصی',
			'image'     => 'proj-bedroom',
			'excerpt'   => 'اتاق خوابی با ترکیب چوب گرم و رنگی شاخص؛ نمونه‌ای از این‌که چگونه یک فضای کوچک می‌تواند شخصیت داشته باشد.',
			'intro'     => 'سوئیت چوب یک واحد کوچک است که می‌بایست هم اتاق خواب، هم فضای کار و هم محل نگهداری لباس را در خود جای دهد. طراحی بر پایه‌ی دیوارهای ذخیره‌سازی پیش رفت.',
			'challenge' => 'متراژ محدود و نیاز به سه کارکرد متفاوت در یک فضا؛ بدون آن‌که فضا فشرده و پرمصرف به نظر برسد.',
			'concept'   => 'کل دیوار روبه‌رو به حجم ذخیره‌سازی تبدیل شد تا کف آزاد بماند. یک رنگ شاخص گرم در بخش پایینی دیوار، عمق بصری و شخصیت ایجاد می‌کند.',
			'materials' => array( 'چوب روکش طبیعی', 'رنگ پلی‌اورتان', 'آینه قدی', 'نور مخفی' ),
			'gallery'   => array( 'proj-bedroom', 'proj-detail', 'proj-dining' ),
		),
		'khane-sangi'     => array(
			'number'    => '07',
			'title'     => 'خانه سنگی',
			'category'  => 'renovation',
			'location'  => 'تهران',
			'style'     => 'بازسازی مدرن',
			'year'      => '۱۴۰۳',
			'area'      => '۱۶۰ متر',
			'client'    => 'خانواده‌ای چهارنفره',
			'image'     => 'proj-kitchen-grey',
			'excerpt'   => 'بازسازی کامل یک آشپزخانه‌ی قدیمی؛ تغییر پوسته، تغییر عملکرد و تغییر تجربه‌ی روزانه‌ی ساکنان.',
			'intro'     => 'خانه سنگی پیش از بازسازی، آشپزخانه‌ای بسته با کابینت‌های چوبی روشن و سطوح گرانیتی داشت. تصمیم بر آن شد که فضا با کمترین تغییر در سازه، بیشترین تغییر را در تجربه ایجاد کند.',
			'challenge' => 'تأسیسات قدیمی، نور کم و چیدمان نامناسب مثلث کاری، باعث می‌شد فضا با وجود متراژ مناسب، کوچک و خسته‌کننده به نظر برسد.',
			'concept'   => 'چیدمان بر اساس مثلث کاری ساماندهی شد و سطوح به رنگ روشن تغییر کرد تا نور بازتاب پیدا کند. صفحه‌ی کابینت با بافت سنگی، نقطه‌ی تمرکز فضا شد.',
			'materials' => array( 'کابینت رنگ شده', 'صفحه کوارتز بافت سنگ', 'کاشی مترو', 'نور زیرکابینتی' ),
			'gallery'   => array( 'proj-kitchen-grey', 'proj-detail', 'proj-kitchen-navy' ),
		),
	);

	return $projects;
}

/**
 * Single project by slug.
 *
 * @param string $slug Project slug.
 * @return array<string, mixed>|null
 */
function hhd_get_project( $slug ) {
	$projects = hhd_projects_data();

	if ( ! isset( $projects[ $slug ] ) ) {
		return null;
	}

	$project         = $projects[ $slug ];
	$project['slug'] = $slug;

	return $project;
}

/**
 * Projects formatted for the front end, with a stable key.
 *
 * @param array<int, string> $only Optional list of slugs to return.
 * @return array<int, array<string, mixed>>
 */
function hhd_projects( $only = array() ) {
	$out = array();
	foreach ( hhd_projects_data() as $slug => $project ) {
		if ( $only && ! in_array( $slug, $only, true ) ) {
			continue;
		}
		$project['slug'] = $slug;
		$out[]           = $project;
	}
	return $out;
}

/**
 * Project categories used by the archive filters.
 *
 * @return array<string, string>
 */
function hhd_project_categories() {
	return array(
		'all'         => 'همه',
		'residential' => 'مسکونی',
		'villa'       => 'ویلا',
		'commercial'  => 'تجاری',
		'office'      => 'اداری',
		'renovation'  => 'بازسازی',
	);
}

/**
 * Human label for a project category key.
 *
 * @param string $key Category key.
 * @return string
 */
function hhd_project_category_label( $key ) {
	$categories = hhd_project_categories();
	return isset( $categories[ $key ] ) ? $categories[ $key ] : $key;
}

/**
 * Services.
 *
 * @return array<string, array<string, mixed>>
 */
function hhd_services_data() {
	static $services = null;
	if ( null !== $services ) {
		return $services;
	}

	$services = array(
		'interior-design'  => array(
			'number'  => '۰۱',
			'title'   => 'طراحی داخلی',
			'summary' => 'طراحی کامل فضا از پلان تا جزئیات اجرایی.',
			'image'   => 'hero-interior',
			'intro'   => 'طراحی داخلی در homehrdecor یک فرآیند است، نه یک تصمیم لحظه‌ای. از شناخت نیازهای ساکنان و محدودیت‌های فضا آغاز می‌شود و تا انتخاب آخرین جزئیات اجرایی ادامه پیدا می‌کند.',
			'features'=> array( 'تحلیل پلان و ساماندهی فضا', 'طراحی کانسپت و مودبورد', 'نقشه‌های اجرایی و دیتیل', 'نظارت بر اجرا' ),
		),
		'home-design'      => array(
			'number'  => '۰۲',
			'title'   => 'طراحی منزل',
			'summary' => 'خانه‌ای متناسب با سبک زندگی شما، نه یک الگوی آماده.',
			'image'   => 'proj-dining',
			'intro'   => 'هر خانه‌ای می‌تواند زیبا باشد؛ اما خانه‌ی خوب، خانه‌ای است که با ریتم روزمره‌ی زندگی شما هماهنگ باشد. طراحی منزل با شناخت همین ریتم شروع می‌شود.',
			'features'=> array( 'طراحی نشیمن و پذیرایی', 'طراحی آشپزخانه و فضای غذا خوری', 'اتاق خواب و فضای کار خانگی', 'ساماندهی نورپردازی' ),
		),
		'villa-design'     => array(
			'number'  => '۰۳',
			'title'   => 'طراحی ویلا',
			'summary' => 'پیوند معماری داخلی با منظره و محیط پیرامون.',
			'image'   => 'proj-kitchen-dark',
			'intro'   => 'در طراحی ویلا، مرز میان داخل و بیرون مهم‌ترین موضوع است. طراحی باید چشم را به منظره هدایت کند و در عین حال فضایی گرم و مستقل بسازد.',
			'features'=> array( 'طراحی فضاهای باز و نیمه‌باز', 'مدیریت مقیاس و ارتفاع', 'انتخاب متریال مقاوم به اقلیم', 'طراحی نورپردازی شبانه' ),
		),
		'commercial-design'=> array(
			'number'  => '۰۴',
			'title'   => 'طراحی فضاهای تجاری',
			'summary' => 'فضایی که تجربه‌ی برند شما را به مشتری منتقل کند.',
			'image'   => 'proj-facade-night',
			'intro'   => 'در فضای تجاری، طراحی داخلی بخشی از تجربه‌ی برند است. هر تصمیم بصری باید با هویت کسب‌وکار و رفتار مشتری هم‌راستا باشد.',
			'features'=> array( 'تحلیل مسیر حرکت مشتری', 'طراحی نما و ویترین', 'هویت بصری فضا', 'طراحی نور و آکوستیک' ),
		),
		'office-design'    => array(
			'number'  => '۰۵',
			'title'   => 'طراحی فضاهای اداری',
			'summary' => 'محیط کاری آرام، کارا و الهام‌بخش برای تیم شما.',
			'image'   => 'proj-detail',
			'intro'   => 'دفتر خوب، محیطی است که تمرکز را ممکن و همکاری را ساده می‌کند. طراحی اداری با شناخت جریان کار تیم آغاز می‌شود.',
			'features'=> array( 'چیدمان فضاهای کار مشترک و خصوصی', 'طراحی اتاق جلسات', 'آکوستیک و کنترل صدا', 'نورپردازی متناسب با کار' ),
		),
		'renovation'       => array(
			'number'  => '۰۶',
			'title'   => 'بازسازی',
			'summary' => 'تغییر کامل فضا با کمترین دست‌کاری در سازه.',
			'image'   => 'proj-kitchen-grey',
			'intro'   => 'بازسازی یعنی بازخوانی یک فضای موجود. بسیاری از فضاها با تغییر پوسته، چیدمان و نور، بدون نیاز به تغییر سازه، کاملاً دگرگون می‌شوند.',
			'features'=> array( 'ارزیابی وضعیت موجود', 'بهینه‌سازی چیدمان', 'بازسازی تأسیسات و سطوح', 'مدیریت زمان و هزینه اجرا' ),
		),
		'3d-visualization' => array(
			'number'  => '۰۷',
			'title'   => 'طراحی سه‌بعدی',
			'summary' => 'دیدن فضای آینده، پیش از شروع اجرا.',
			'image'   => 'proj-bedroom',
			'intro'   => 'تصویر سه‌بعدی، زبان مشترک کارفرما و مجری است. پیش از خرید هر متریال، می‌توانید فضا را ببینید و درباره‌اش تصمیم بگیرید.',
			'features'=> array( 'مدل‌سازی دقیق فضا', 'رندر واقع‌گرای متریال و نور', 'بازبینی و اصلاح طرح', 'خروجی برای ارائه به مجری' ),
		),
		'materials'        => array(
			'number'  => '۰۸',
			'title'   => 'انتخاب متریال و جزئیات',
			'summary' => 'انتخاب درست جنس‌ها؛ تفاوت طراحی خوب و ماندگار.',
			'image'   => 'proj-detail',
			'intro'   => 'بسیاری از فضاها در مرحله‌ی انتخاب متریال از مسیر خارج می‌شوند. همراهی در این مرحله تضمین می‌کند طرح، همان‌طور که تصور شده، اجرا شود.',
			'features'=> array( 'انتخاب متریال متناسب با کاربری', 'تنظیم پالت جنس و رنگ', 'طراحی جزئیات اجرایی', 'همراهی در خرید و کنترل کیفیت' ),
		),
	);

	return $services;
}

/**
 * Single service by slug.
 *
 * @param string $slug Service slug.
 * @return array<string, mixed>|null
 */
function hhd_get_service( $slug ) {
	$services = hhd_services_data();

	if ( ! isset( $services[ $slug ] ) ) {
		return null;
	}

	$service         = $services[ $slug ];
	$service['slug'] = $slug;

	return $service;
}

/**
 * Services formatted for the front end.
 *
 * @param array<int, string> $only Optional list of slugs.
 * @return array<int, array<string, mixed>>
 */
function hhd_services( $only = array() ) {
	$out = array();
	foreach ( hhd_services_data() as $slug => $service ) {
		if ( $only && ! in_array( $slug, $only, true ) ) {
			continue;
		}
		$service['slug'] = $slug;
		$out[]           = $service;
	}
	return $out;
}

/**
 * Design principles shown in the dark philosophy section.
 *
 * @return array<int, array<string, string>>
 */
function hhd_principles() {
	return array(
		array(
			'number' => '۰۱',
			'title'  => 'عملکرد',
			'text'   => 'زیبایی زمانی ارزشمند است که با زندگی روزمره هماهنگ باشد.',
		),
		array(
			'number' => '۰۲',
			'title'  => 'هویت',
			'text'   => 'هر فضا باید بازتابی از شخصیت و سبک زندگی استفاده‌کنندگان آن باشد.',
		),
		array(
			'number' => '۰۳',
			'title'  => 'جزئیات',
			'text'   => 'تفاوت یک طراحی خوب و یک طراحی ماندگار در جزئیات آن است.',
		),
	);
}

/**
 * Design process steps.
 *
 * @return array<int, array<string, string>>
 */
function hhd_process_steps() {
	return array(
		array(
			'number' => '۰۱',
			'title'  => 'شناخت',
			'text'   => 'گفت‌وگو، بازدید فضا و شناخت نیازها، محدودیت‌ها و سبک زندگی کارفرما.',
		),
		array(
			'number' => '۰۲',
			'title'  => 'ایده‌پردازی',
			'text'   => 'تبدیل شناخت به کانسپت؛ مودبورد، پالت متریال و جهت‌گیری کلی طراحی.',
		),
		array(
			'number' => '۰۳',
			'title'  => 'طراحی',
			'text'   => 'تدوین پلان نهایی، نقشه‌های اجرایی و تصاویر سه‌بعدی فضا.',
		),
		array(
			'number' => '۰۴',
			'title'  => 'انتخاب متریال',
			'text'   => 'انتخاب جنس‌ها، رنگ‌ها و جزئیات اجرایی همراه با کارفرما.',
		),
		array(
			'number' => '۰۵',
			'title'  => 'اجرا',
			'text'   => 'همراهی و نظارت بر اجرا تا اطمینان از تطابق نتیجه با طرح.',
		),
		array(
			'number' => '۰۶',
			'title'  => 'تحویل',
			'text'   => 'بازبینی نهایی جزئیات و تحویل فضایی آماده‌ی زندگی.',
		),
	);
}

/**
 * Studio statistics.
 *
 * These are placeholder figures for the demo layout and are not
 * verified claims about the studio.
 *
 * Values are kept as Latin digits so the counter script can parse them;
 * the templates render them as Persian numerals.
 *
 * @return array<int, array<string, mixed>>
 */
function hhd_stats() {
	return array(
		array(
			'value' => '40',
			'plus'  => true,
			'label' => 'پروژه',
		),
		array(
			'value' => '8',
			'plus'  => true,
			'label' => 'سال تجربه',
		),
		array(
			'value' => '15',
			'plus'  => true,
			'label' => 'پروژه مسکونی',
		),
		array(
			'value' => '10',
			'plus'  => true,
			'label' => 'پروژه تجاری',
		),
	);
}

/**
 * Demo testimonials — placeholder content.
 *
 * @return array<int, array<string, string>>
 */
function hhd_testimonials() {
	return array(
		array(
			'name'    => 'سارا م.',
			'project' => 'بازسازی آپارتمان، تهران',
			'quote'   => 'از اولین جلسه مشخص بود که قرار نیست یک طرح آماده تحویل بگیریم. تمام تصمیم‌ها بر اساس زندگی خودمان گرفته شد و نتیجه، خانه‌ای است که واقعاً در آن راحت هستیم.',
		),
		array(
			'name'    => 'امیر ک.',
			'project' => 'طراحی ویلای شخصی، اصفهان',
			'quote'   => 'بزرگ‌ترین دغدغه‌مان هماهنگی فضا با منظره بود. تیم homehrdecor با دقت روی جزئیات نور و متریال کار کرد و نتیجه از تصویری که در ذهن داشتیم هم بهتر شد.',
		),
		array(
			'name'    => 'نگار ر.',
			'project' => 'طراحی فضای تجاری، اصفهان',
			'quote'   => 'برای یک کسب‌وکار تازه، هویت فضا اهمیت زیادی داشت. طراحی نما و داخل با هم انجام شد و مشتری‌ها همین را به ما می‌گویند که فضا حس متفاوتی دارد.',
		),
	);
}

/**
 * Demo journal articles used when no posts exist yet.
 *
 * @return array<int, array<string, string>>
 */
function hhd_journal_fallback() {
	return array(
		array(
			'category' => 'راهنما',
			'title'    => 'چگونه سبک مناسب دکوراسیون خانه را انتخاب کنیم؟',
			'excerpt'  => 'انتخاب سبک، پیش از خرید مبل و انتخاب رنگ اتفاق می‌افتد. در این راهنما مسیر رسیدن به سبک شخصی را مرور می‌کنیم.',
			'image'    => 'proj-dining',
			'date'     => '۱۴۰۵/۰۴/۱۲',
		),
		array(
			'category' => 'اشتباهات رایج',
			'title'    => '۷ اشتباه رایج در طراحی داخلی',
			'excerpt'  => 'بسیاری از فضاهای ناتمام، نتیجه‌ی تصمیم‌های کوچکی هستند که در زمان اشتباه گرفته شده‌اند.',
			'image'    => 'proj-kitchen-grey',
			'date'     => '۱۴۰۵/۰۳/۲۸',
		),
		array(
			'category' => 'فضاهای کوچک',
			'title'    => 'چگونه یک فضای کوچک را بزرگ‌تر نشان دهیم؟',
			'excerpt'  => 'متراژ کم، محدودیت نهایی نیست. با چند اصل ساده می‌توان عمق و وسعت بصری ایجاد کرد.',
			'image'    => 'proj-bedroom',
			'date'     => '۱۴۰۵/۰۳/۰۹',
		),
		array(
			'category' => 'متریال',
			'title'    => 'راهنمای انتخاب متریال در طراحی داخلی',
			'excerpt'  => 'جنس‌ها بیش از رنگ‌ها بر کیفیت ادراک‌شده‌ی فضا اثر می‌گذارند. راهنمای عملی انتخاب متریال.',
			'image'    => 'proj-detail',
			'date'     => '۱۴۰۵/۰۲/۲۱',
		),
	);
}

/**
 * Studio contact details.
 *
 * Demo values — replace with the studio's real details before launch.
 *
 * @return array<string, mixed>
 */
function hhd_contact() {
	return array(
		'phone_label' => '09135501841',
		'phone_href'  => '+989135501841',
		'email'       => 'hello@homehrdecor.com',
		'instagram'   => 'home_mehr_decor',
		'address'     => '',
		'address_note'=> 'بازدید از استودیو با هماهنگی قبلی انجام می‌شود.',
		'hours'       => 'شنبه تا چهارشنبه، ۱۰ تا ۱۸',
		'hours_note'  => 'پنجشنبه‌ها با تعیین وقت قبلی',
	);
}

/**
 * Project types offered in the contact and consultation forms.
 *
 * @return array<int, string>
 */
function hhd_project_types() {
	return array(
		'طراحی داخلی منزل',
		'طراحی ویلا',
		'فضای تجاری',
		'فضای اداری',
		'بازسازی',
		'طراحی سه‌بعدی',
		'مشاوره و انتخاب متریال',
	);
}

/**
 * Frequently asked questions, shared by service pages and the CTA.
 *
 * @return array<int, array<string, string>>
 */
function hhd_faqs() {
	return array(
		array(
			'q' => 'فرآیند طراحی معمولاً چقدر زمان می‌برد؟',
			'a' => 'بازه‌ی زمانی به متراژ و پیچیدگی پروژه بستگی دارد؛ اما در حالت معمول، از جلسه‌ی شناخت تا تحویل نقشه‌های اجرایی بین چهار تا ده هفته است.',
		),
		array(
			'q' => 'آیا امکان طراحی برای پروژه‌های خارج از اصفهان وجود دارد؟',
			'a' => 'بله. بخش عمده‌ی فرآیند طراحی از راه دور قابل انجام است و برای بازدیدهای کلیدی، هماهنگی سفر انجام می‌شود.',
		),
		array(
			'q' => 'هزینه‌ی طراحی چگونه محاسبه می‌شود؟',
			'a' => 'هزینه بر اساس متراژ، دامنه‌ی خدمات و سطح جزئیات تعیین می‌شود. پس از جلسه‌ی اولیه و مشخص شدن دامنه‌ی کار، پیشنهاد مالی ارائه می‌شود.',
		),
		array(
			'q' => 'آیا در مرحله‌ی اجرا هم همراهی می‌کنید؟',
			'a' => 'بله. نظارت بر اجرا بخشی از خدمات است تا نتیجه‌ی نهایی با طرح مورد توافق تطابق داشته باشد.',
		),
	);
}
