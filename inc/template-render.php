<?php
defined( 'ABSPATH' ) || exit;

/**
 * Resolve which template (dimu_template post) applies for the current request.
 *
 * Pages: per-page override (inherit / none / custom).
 * Other singulars: per-CPT default from options.
 * Everything else: global default from options.
 *
 * @param string $type Template type slug ('footer', 'header').
 * @return int Post ID, or 0 for none.
 */
function dimu_child_resolve_template( string $type ): int {
	$id = 0;

	if ( is_page() ) {
		$page_id = get_queried_object_id();
		$mode    = (string) get_field( $type . '_mode', $page_id );

		if ( 'none' === $mode ) {
			return 0;
		}
		if ( 'custom' === $mode ) {
			$id = (int) get_field( $type . '_template', $page_id );
		}
	} elseif ( is_singular() ) {
		$id = (int) get_field( 'default_' . $type . '_template_' . get_post_type(), 'option' );
	}

	if ( ! $id ) {
		$id = (int) get_field( 'default_' . $type . '_template', 'option' );
	}

	return dimu_child_validate_template( $id, $type );
}

/**
 * A resolved ID must be a published dimu_template with the right type term.
 * Guards against deleted/drafted templates or a wrong pick ever breaking output.
 */
function dimu_child_validate_template( int $id, string $type ): int {
	if ( ! $id ) {
		return 0;
	}
	$post = get_post( $id );
	if ( ! $post || 'dimu_template' !== $post->post_type || 'publish' !== $post->post_status ) {
		return 0;
	}
	return has_term( $type, 'template_type', $post ) ? $id : 0;
}

/**
 * Render a resolved template via its PHP partial.
 * Partial: template-parts/site-template-{type}.php, receives the template post ID.
 *
 * @param string $type Template type slug.
 */
function dimu_child_render_template( string $type ): void {
	$id = dimu_child_resolve_template( $type );
	if ( ! $id ) {
		return;
	}

	$tag  = 'header' === $type ? 'header' : 'footer';
	$role = 'header' === $type ? 'banner' : 'contentinfo';

	printf(
		'<%1$s class="site-%2$s site-%2$s--%3$d" role="%4$s">',
		$tag, // phpcs:ignore WordPress.Security.EscapeOutput
		esc_attr( $type ),
		$id,
		esc_attr( $role )
	);

	// Styles live in the scss pipeline (partials/_{type}.scss -> one.css).
	get_template_part( 'template-parts/site-template', $type, array( 'template_id' => $id ) );

	printf( '</%s>', $tag ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Restrict template-picker post object fields to the matching type.
 * Applies to any ACF field named default_{type}, default_{type}_*, or {type}_template.
 */
function dimu_child_filter_template_field_query( array $args, array $field ): array {
	foreach ( array( 'footer', 'header' ) as $type ) {
		if ( preg_match( '/^(default_' . $type . '(_|$)|' . $type . '_template$)/', $field['name'] ) ) {
			$args['post_type'] = 'dimu_template';
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => 'template_type',
					'field'    => 'slug',
					'terms'    => $type,
				),
			);
			break;
		}
	}
	return $args;
}
add_filter( 'acf/fields/post_object/query', 'dimu_child_filter_template_field_query', 10, 2 );

/**
 * Populate template-picker selects with published templates of the right type.
 * Matches: default_{type}_template and {type}_template.
 */
function dimu_child_populate_template_choices( array $field ): array {
	foreach ( array( 'footer', 'header' ) as $type ) {
		if ( ! preg_match( '/(^|_)' . $type . '_template$/', $field['name'] ) ) {
			continue;
		}
		$posts = get_posts( array(
			'post_type'              => 'dimu_template',
			'post_status'            => 'publish',
			'posts_per_page'         => 100,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'fields'                 => 'ids',
			'tax_query'              => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => 'template_type',
					'field'    => 'slug',
					'terms'    => $type,
				),
			),
		) );

		$field['choices'] = array();
		foreach ( $posts as $post_id ) {
			$field['choices'][ $post_id ] = get_the_title( $post_id );
		}
		break;
	}
	return $field;
}
add_filter( 'acf/load_field/name=default_footer_template', 'dimu_child_populate_template_choices' );
add_filter( 'acf/load_field/name=default_header_template', 'dimu_child_populate_template_choices' );
add_filter( 'acf/load_field/name=footer_template', 'dimu_child_populate_template_choices' );
add_filter( 'acf/load_field/name=header_template', 'dimu_child_populate_template_choices' );
