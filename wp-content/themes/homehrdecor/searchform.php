<?php
/**
 * Search form.
 *
 * @package homehrdecor
 */

defined( 'ABSPATH' ) || exit;

$hhd_search_id = 'search-' . wp_unique_id();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $hhd_search_id ); ?>">
		<span class="sr-only"><?php esc_html_e( 'عبارت مورد جست‌وجو', 'homehrdecor' ); ?></span>
	</label>
	<input
		type="search"
		id="<?php echo esc_attr( $hhd_search_id ); ?>"
		class="search-field"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
		placeholder="<?php esc_attr_e( 'جست‌وجو در سایت…', 'homehrdecor' ); ?>"
	>
	<button type="submit" class="search-submit"><?php esc_html_e( 'جست‌وجو', 'homehrdecor' ); ?></button>
</form>
