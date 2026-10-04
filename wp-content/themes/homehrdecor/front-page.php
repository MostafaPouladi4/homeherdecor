<?php
/**
 * Home page.
 *
 * Order of sections follows the design brief: hero, brand introduction,
 * featured projects, services, philosophy, before/after, process, about,
 * testimonials, journal, consultation CTA.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<?php get_template_part( 'template-parts/hero/home' ); ?>

<?php get_template_part( 'template-parts/sections/brand-intro' ); ?>

<?php get_template_part( 'template-parts/sections/featured-projects' ); ?>

<?php get_template_part( 'template-parts/sections/services' ); ?>

<?php get_template_part( 'template-parts/sections/philosophy' ); ?>

<?php get_template_part( 'template-parts/sections/before-after' ); ?>

<?php get_template_part( 'template-parts/sections/process' ); ?>

<?php get_template_part( 'template-parts/sections/about-studio' ); ?>

<?php get_template_part( 'template-parts/sections/testimonials' ); ?>

<?php get_template_part( 'template-parts/sections/journal' ); ?>

<?php
hhd_cta(
	array(
		'eyebrow' => 'شروع یک پروژه',
		'title'   => 'فضای بعدی شما از همین‌جا شروع می‌شود.',
		'text'    => 'اگر برای طراحی، بازسازی یا تغییر فضای خود به دنبال یک راهکار اختصاصی هستید، با ما در ارتباط باشید.',
	)
);
?>

<?php
get_footer();
