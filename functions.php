<?php
/**
 * DimuOne Child theme bootstrap.
 *
 * The parent framework (inc/, optimization, caching, settings) loads from
 * get_template_directory(), so everything keeps working from here. This file
 * is for site-specific hooks only.
 *
 * @package DIMU\Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Child styles, after the parent's global assets: the Grunt build outputs
 * (vendor = normalize, bundle = the site styles) when built, then style.css.
 * dimu-* handles so the parent Asset_Optimizer combines/minifies them.
 */
function dimu_child_enqueue_style(): void {
	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	// Google Fonts: Inter (main) + Libre Caslon Text (secondary/headings).
	// The parent's Font_Host localizes this to uploads/dimu-fonts (GDPR) and
	// preloads the woff2. Non dimu- handle so the optimizer leaves it alone.
	wp_enqueue_style(
		'graphindy-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400..700&family=Libre+Caslon+Text:ital,wght@0,400;0,700;1,400&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	);

	foreach ( array( 'dimu-vendor' => '/assets/css/vendor.css', 'dimu-one' => '/assets/css/one.css' ) as $handle => $rel ) {
		if ( file_exists( $dir . $rel ) ) {
			wp_enqueue_style( $handle, $uri . $rel, array(), (string) filemtime( $dir . $rel ) );
		}
	}

	wp_enqueue_style(
		'dimu-child',
		get_stylesheet_uri(),
		array(),
		(string) filemtime( $dir . '/style.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'dimu_child_enqueue_style', 20 );

require_once get_stylesheet_directory() . '/inc/child-helpers.php';

// Site templates system: CPT, ACF options, resolver/render, admin preview.
require_once get_stylesheet_directory() . '/inc/cpt/templates.php';
require_once get_stylesheet_directory() . '/inc/theme-options.php';
require_once get_stylesheet_directory() . '/inc/template-render.php';
require_once get_stylesheet_directory() . '/inc/template-preview.php';

