<?php
defined( 'ABSPATH' ) || exit;

/**
 * "Templates" CPT: reusable site parts (footer, header, ...) built with the block editor.
 * Fully private on the front end — rendered only via the theme's resolver.
 */
function dimu_child_register_templates_cpt(): void {
	register_post_type( 'dimu_template', array(
		'labels'              => array(
			'name'          => __( 'Templates', 'dimuone-child' ),
			'singular_name' => __( 'Template', 'dimuone-child' ),
			'add_new_item'  => __( 'Add New Template', 'dimuone-child' ),
			'edit_item'     => __( 'Edit Template', 'dimuone-child' ),
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false, // Block editor.
		'menu_position'       => 58,
		'menu_icon'           => 'dashicons-layout',
		'supports'            => array( 'title', 'revisions' ),
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'rewrite'             => false,
		'query_var'           => false,
		'show_in_nav_menus'   => false,
		'capability_type'     => 'page',
	) );

	register_taxonomy( 'template_type', 'dimu_template', array(
		'labels'            => array(
			'name'          => __( 'Template Types', 'dimuone-child' ),
			'singular_name' => __( 'Template Type', 'dimuone-child' ),
		),
		'public'            => false,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'hierarchical'      => true, // Checkbox UI in the editor sidebar.
		'show_admin_column' => true,
		'rewrite'           => false,
		'query_var'         => false,
	) );
}
add_action( 'init', 'dimu_child_register_templates_cpt' );

/**
 * Seed the base terms once, on theme activation.
 */
function dimu_child_seed_template_types(): void {
	foreach ( array( 'Footer' => 'footer', 'Header' => 'header' ) as $name => $slug ) {
		if ( ! term_exists( $slug, 'template_type' ) ) {
			wp_insert_term( $name, 'template_type', array( 'slug' => $slug ) );
		}
	}
}
add_action( 'after_switch_theme', 'dimu_child_seed_template_types' );

/**
 * Filter-by-type dropdown in the Templates list table.
 */
function dimu_child_template_type_filter(): void {
	global $typenow;
	if ( 'dimu_template' !== $typenow ) {
		return;
	}
	wp_dropdown_categories( array(
		'taxonomy'        => 'template_type',
		'name'            => 'template_type',
		'value_field'     => 'slug',
		'selected'        => sanitize_key( $_GET['template_type'] ?? '' ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		'show_option_all' => __( 'All types', 'dimuone-child' ),
		'hide_empty'      => false,
	) );
}
add_action( 'restrict_manage_posts', 'dimu_child_template_type_filter' );
