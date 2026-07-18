<?php

defined( 'ABSPATH' ) || exit;

/**
 * Collection of various helper functions
 */

/**
 * Returns an image url inside the child theme
 *
 * @param [string] $img The image filename
 * @return string The image full path
 */
function get_local_img_url( $img ) {

	return get_stylesheet_directory_uri() . "/assets/img/{$img}";

}

/**
 * Returns an icon url inside the theme
 * Same as get_local_img_url() but it goes in the /icons subfolder
 *
 * @param [string] $img The icon filename
 * @return string The icon full path
 */
function get_icon_url( $icon ) {

	return get_local_img_url( "icons/{$icon}" );

}

/**
 * A quick shorthand to get an ACF field for a taxonomy (by ID/Object)
 *
 * @param [string] $field The ACF field we need
 * @param [int|object] $term The taxonomy term (ID/Object)
 * @param string $taxonomy The taxonomy name
 * @return mixed The field value
 */
function get_taxonomy_field( $field, $term, $taxonomy = '' ) {

	if ( ! is_object( $term ) ) {
		return get_field( $field, "{$taxonomy}_{$term}" );
	}

	return get_field( $field, "{$term->taxonomy}_{$term->term_id}" );

}

/**
 * A simple and quick way to check if a post has content or not
 *
 * @param [int] $post_id The post ID
 * @return boolean Returns true if the post has content, false otherwise
 */
function has_content( $post_id = null ) {

	if ( is_null( $post_id ) ) {
		$post_id = get_the_ID();
	}

	$content = (string) get_post_field( 'post_content', $post_id );

	return trim( str_replace( '&nbsp;', '', wp_strip_all_tags( $content ) ) ) !== '';

}
