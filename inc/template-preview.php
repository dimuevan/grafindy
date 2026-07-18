<?php
defined( 'ABSPATH' ) || exit;

/**
 * Front-end preview endpoint for dimu_template posts.
 * URL: /?dimu_template_preview={id}&_wpnonce=... (editors only).
 */
function dimu_child_template_preview_endpoint(): void {
	$id = (int) ( $_GET['dimu_template_preview'] ?? 0 );
	if ( ! $id ) {
		return;
	}
	if (
		! current_user_can( 'edit_post', $id )
		|| ! wp_verify_nonce( (string) ( $_GET['_wpnonce'] ?? '' ), 'dimu_template_preview_' . $id )
	) {
		wp_die( esc_html__( 'Not allowed.', 'dimuone-child' ), 403 );
	}

	$post = get_post( $id );
	if ( ! $post || 'dimu_template' !== $post->post_type ) {
		wp_die( esc_html__( 'Invalid template.', 'dimuone-child' ), 404 );
	}

	$terms = get_the_terms( $id, 'template_type' );
	$type  = $terms && ! is_wp_error( $terms ) ? $terms[0]->slug : '';

	nocache_headers();
	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex">
		<?php wp_head(); ?>
		<style>body { margin: 0; } #wpadminbar { display: none; } html { margin-top: 0 !important; }</style>
	</head>
	<body <?php body_class( 'dimu-template-preview' ); ?>>
	<?php
	if ( $type ) {
		$tag = 'header' === $type ? 'header' : 'footer';
		printf( '<%s class="site-%s site-%s--%d">', $tag, esc_attr( $type ), esc_attr( $type ), $id ); // phpcs:ignore WordPress.Security.EscapeOutput
		get_template_part( 'template-parts/site-template', $type, array( 'template_id' => $id ) );
		printf( '</%s>', $tag ); // phpcs:ignore WordPress.Security.EscapeOutput
	} else {
		esc_html_e( 'Assign a Template Type (footer/header) and save to preview.', 'dimuone-child' );
	}
	wp_footer();
	?>
	</body>
	</html>
	<?php
	exit;
}
add_action( 'template_redirect', 'dimu_child_template_preview_endpoint', 0 );

/**
 * Live preview metabox (iframe) on the dimu_template edit screen.
 */
function dimu_child_template_preview_metabox(): void {
	add_meta_box(
		'dimu-template-preview',
		__( 'Preview', 'dimuone-child' ),
		'dimu_child_render_preview_metabox',
		'dimu_template',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'dimu_child_template_preview_metabox' );

function dimu_child_render_preview_metabox( WP_Post $post ): void {
	if ( 'auto-draft' === $post->post_status ) {
		echo '<p>' . esc_html__( 'Save the template to see a preview.', 'dimuone-child' ) . '</p>';
		return;
	}
	$url = wp_nonce_url(
		add_query_arg( 'dimu_template_preview', $post->ID, home_url( '/' ) ),
		'dimu_template_preview_' . $post->ID
	);
	printf(
		'<iframe src="%s" style="width:100%%;height:420px;border:0;background:#fff;" loading="lazy" title="%s"></iframe>',
		esc_url( $url ),
		esc_attr__( 'Template preview', 'dimuone-child' )
	);
}
