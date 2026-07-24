<?php
/**
 * Header template partial — GRAPHINDY transparent header.
 *
 * Style one (Default): logo left, menu + pill CTA right.
 * Style two (Landing): centered logo only.
 * Transparent background — the header overlays the page hero.
 *
 * Rendered by dimu_child_render_template() and the preview endpoint inside
 * <header class="site-header">. Reads ACF fields from $args['template_id'].
 * Styles: assets/src/scss/partials/_header.scss (compiled into one.css).
 */

defined( 'ABSPATH' ) || exit;

$template_id = (int) ( $args['template_id'] ?? 0 );
if ( ! $template_id ) {
	return;
}

$style   = (string) ( get_field( 'style', $template_id ) ?: 'one' );
$logo_id = (int) get_field( 'header_logo', $template_id );
$menu_id = 'one' === $style ? (int) get_field( 'navigation', $template_id ) : 0;
$cta     = 'one' === $style ? (array) get_field( 'cta_button', $template_id ) : array();
$menu    = $menu_id ? wp_get_nav_menu_object( $menu_id ) : null;
?>
<div class="header header--<?php echo esc_attr( $style ); ?> container">

	<?php if ( $logo_id ) : ?>
	<a class="header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		<?php
		echo wp_get_attachment_image( $logo_id, 'medium', false, array(
			'class' => 'header__logo-img',
		) );
		?>
	</a>
	<?php endif; ?>

	<?php if ( $menu || $cta ) : ?>
	<div class="header__end">

		<?php if ( $menu ) : ?>
		<nav class="header__nav" aria-label="<?php echo esc_attr( $menu->name ); ?>">
			<?php
			wp_nav_menu( array(
				'menu'        => $menu_id,
				'container'   => false,
				'fallback_cb' => false,
				'depth'       => 2,
			) );
			?>
		</nav>
		<?php endif; ?>

		<?php if ( ! empty( $cta['url'] ) ) : ?>
		<a class="header__cta"
			href="<?php echo esc_url( $cta['url'] ); ?>"
			<?php if ( ! empty( $cta['target'] ) ) : ?>target="<?php echo esc_attr( $cta['target'] ); ?>" rel="noopener"<?php endif; ?>>
			<?php echo esc_html( $cta['title'] ?: __( "Let's Chat", 'dimuone-child' ) ); ?>
		</a>
		<?php endif; ?>

	</div>
	<?php endif; ?>

</div>
