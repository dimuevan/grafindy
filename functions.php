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
 * Child stylesheet, after the parent's global assets.
 */
function dimu_child_enqueue_style(): void {
	wp_enqueue_style(
		'dimu-child',
		get_stylesheet_uri(),
		array(),
		(string) filemtime( get_stylesheet_directory() . '/style.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'dimu_child_enqueue_style', 20 );

// Site templates system: CPT, ACF options, resolver/render, admin preview.
require_once get_stylesheet_directory() . '/inc/cpt/templates.php';
require_once get_stylesheet_directory() . '/inc/theme-options.php';
require_once get_stylesheet_directory() . '/inc/template-render.php';
require_once get_stylesheet_directory() . '/inc/template-preview.php';

