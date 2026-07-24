<?php
defined( 'ABSPATH' ) || exit;

/**
 * ACF "Theme Options" page + dynamic choices for template fields.
 */

/**
 * Options page the "Theme Options" field group targets (menu_slug matches
 * the acf-json location rule).
 */
function dimu_child_register_options_page(): void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}
	if ( function_exists( 'acf_get_options_page' ) && acf_get_options_page( 'theme-options' ) ) {
		return; // Already registered elsewhere (e.g. ACF UI).
	}
	acf_add_options_page( array(
		'page_title' => __( 'Theme Options', 'dimuone-child' ),
		'menu_title' => __( 'Theme Options', 'dimuone-child' ),
		'menu_slug'  => 'theme-options',
		'capability' => 'manage_options',
		'position'   => 59,
		'icon_url'   => 'dashicons-admin-customizer',
		'autoload'   => true,
	) );
}
add_action( 'acf/init', 'dimu_child_register_options_page' );

/**
 * Populate "Navigations" selects with the registered nav menus.
 */
function dimu_child_populate_nav_menu_choices( array $field ): array {
	$field['choices'] = array();
	foreach ( wp_get_nav_menus() as $menu ) {
		$field['choices'][ $menu->term_id ] = $menu->name;
	}
	return $field;
}
add_filter( 'acf/load_field/name=navigations', 'dimu_child_populate_nav_menu_choices' );
add_filter( 'acf/load_field/name=navigation', 'dimu_child_populate_nav_menu_choices' );

/**
 * Populate "Choose Socials" with the rows of the global Social Icons repeater.
 * Values are row indexes — reordering rows in Theme Options shifts them, so
 * re-check templates with a custom selection after reordering.
 */
function dimu_child_populate_social_choices( array $field ): array {
	$field['choices'] = array();

	$rows = get_field( 'global_social_icons', 'option' );
	if ( ! is_array( $rows ) ) {
		return $field;
	}

	foreach ( array_values( $rows ) as $i => $row ) {
		$label = dimu_child_social_label( (array) ( $row['link'] ?? array() ) );
		/* translators: %d: row number in the Social Icons repeater. */
		$field['choices'][ $i ] = $label ? $label : sprintf( __( 'Social #%d', 'dimuone-child' ), $i + 1 );
	}
	return $field;
}
add_filter( 'acf/load_field/name=choose_socials', 'dimu_child_populate_social_choices' );

/**
 * Human label for a social row: link title, else the URL host.
 *
 * @param array $link ACF link value (url/title/target).
 */
function dimu_child_social_label( array $link ): string {
	if ( ! empty( $link['title'] ) ) {
		return (string) $link['title'];
	}
	if ( ! empty( $link['url'] ) ) {
		$host = wp_parse_url( (string) $link['url'], PHP_URL_HOST );
		return $host ? preg_replace( '/^www\./', '', $host ) : (string) $link['url'];
	}
	return '';
}
