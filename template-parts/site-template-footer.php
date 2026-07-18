<?php
/**
 * Footer template partial — GRAPHINDY landing footer.
 *
 * Layout (top to bottom): nav sections (menu name + link grid, external links
 * get an arrow), socials row, copyright/credit row, small-print about text,
 * giant brand mark clipped by the rounded bottom edge, then the subscribe
 * band (CTA title + form shortcode + note) on the form background color.
 *
 * Rendered by dimu_child_render_template() and the preview endpoint inside
 * <footer class="site-footer">. Reads ACF fields from $args['template_id'].
 * Styles: assets/src/scss/partials/_footer.scss (compiled into one.css).
 */

defined( 'ABSPATH' ) || exit;

$template_id = (int) ( $args['template_id'] ?? 0 );
if ( ! $template_id ) {
	return;
}

$style     = (string) ( get_field( 'style', $template_id ) ?: 'one' );
$cta       = (array) get_field( 'cta_section', $template_id );
$menu_ids  = array_filter( array_map( 'intval', (array) get_field( 'navigations', $template_id ) ) );
$about     = (string) get_field( 'about_text', $template_id );
$copyright = (string) get_field( 'copyrights_text', $template_id );
$logo_id   = (int) get_field( 'footer_logo', $template_id );
$form      = trim( (string) get_field( 'subscription_form_shortcode', $template_id ) );

// Socials: every row of the global repeater, or only the selected row indexes.
$social_rows = get_field( 'global_social_icons', 'option' );
$social_rows = is_array( $social_rows ) ? array_values( $social_rows ) : array();
if ( 'custom' === get_field( 'socials', $template_id ) ) {
	$picked      = array_map( 'intval', (array) get_field( 'choose_socials', $template_id ) );
	$social_rows = array_intersect_key( $social_rows, array_flip( $picked ) );
}

$color_vars = sprintf(
	'--footer-bg:%s;--footer-text:%s;--footer-form-bg:%s;',
	sanitize_hex_color( (string) get_field( 'footer_background', $template_id ) ) ?: '#F2F1F3',
	sanitize_hex_color( (string) get_field( 'footer_text_color', $template_id ) ) ?: '#111111',
	sanitize_hex_color( (string) get_field( 'footer_form_background', $template_id ) ) ?: '#000000'
);

$cta_link = (array) ( $cta['button_link'] ?? array() );
$has_band = $form || ! empty( $cta['title'] ) || ! empty( $cta_link['url'] );

// Flag off-site menu links so CSS can append the ↗ arrow.
$dimu_child_flag_external = static function ( $atts ) {
	$host = wp_parse_url( (string) ( $atts['href'] ?? '' ), PHP_URL_HOST );
	if ( $host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $host ) {
		$atts['class']  = trim( ( $atts['class'] ?? '' ) . ' is-external' );
		$atts['target'] = '_blank';
		$atts['rel']    = 'noopener';
	}
	return $atts;
};
?>
<div class="footer footer--<?php echo esc_attr( $style ); ?>" style="<?php echo esc_attr( $color_vars ); ?>">

	<div class="footer__top">
		<div class="container">

			<?php
			foreach ( $menu_ids as $menu_id ) :
				$menu = wp_get_nav_menu_object( $menu_id );
				if ( ! $menu ) {
					continue;
				}
				?>
			<nav class="footer__nav" aria-label="<?php echo esc_attr( $menu->name ); ?>">
				<h3 class="footer__nav-title"><?php echo esc_html( $menu->name ); ?></h3>
				<?php
				add_filter( 'nav_menu_link_attributes', $dimu_child_flag_external );
				wp_nav_menu( array(
					'menu'        => $menu_id,
					'container'   => false,
					'fallback_cb' => false,
					'depth'       => 1,
				) );
				remove_filter( 'nav_menu_link_attributes', $dimu_child_flag_external );
				?>
			</nav>
			<?php endforeach; ?>

			<?php if ( $social_rows ) : ?>
			<ul class="footer__socials unlist">
				<?php
				foreach ( $social_rows as $row ) :
					$icon_id = (int) ( $row['icon'] ?? 0 );
					$link    = (array) ( $row['link'] ?? array() );
					if ( ! $icon_id || empty( $link['url'] ) ) {
						continue;
					}
					$label = dimu_child_social_label( $link );
					?>
				<li>
					<a href="<?php echo esc_url( $link['url'] ); ?>"
						target="<?php echo esc_attr( $link['target'] ?: '_blank' ); ?>"
						rel="noopener"
						aria-label="<?php echo esc_attr( $label ); ?>">
						<?php
						echo wp_get_attachment_image( $icon_id, 'thumbnail', false, array(
							'class'   => 'footer__social-icon',
							'loading' => 'lazy',
							'alt'     => $label,
						) );
						?>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>

			<div class="footer__meta">
				<?php if ( $copyright ) : ?>
				<p class="footer__copyright"><?php echo wp_kses_post( nl2br( $copyright ) ); ?></p>
				<?php endif; ?>
				<p class="footer__credit">
					<?php esc_html_e( 'Designed & Developed by', 'dimuone-child' ); ?>
					<a href="https://dimu.studio" target="_blank" rel="noopener">DIMU.STUDIO</a>
				</p>
			</div>

			<?php if ( $about ) : ?>
			<div class="footer__about"><?php echo wp_kses_post( $about ); ?></div>
			<?php endif; ?>

			<?php if ( $logo_id ) : ?>
			<div class="footer__brand">
				<?php
				echo wp_get_attachment_image( $logo_id, 'large', false, array(
					'class'   => 'footer__brand-mark',
					'loading' => 'lazy',
					'alt'     => '',
				) );
				?>
			</div>
			<?php endif; ?>

		</div>
	</div>

	<?php if ( $has_band ) : ?>
	<div class="footer__subscribe">
		<div class="container text-center">
			<?php if ( ! empty( $cta['title'] ) ) : ?>
			<h2 class="footer__subscribe-title"><?php echo esc_html( $cta['title'] ); ?></h2>
			<?php endif; ?>

			<?php if ( $form ) : ?>
			<div class="footer__form"><?php echo do_shortcode( $form ); ?></div>
			<?php elseif ( ! empty( $cta_link['url'] ) ) : ?>
			<a class="footer__subscribe-button"
				href="<?php echo esc_url( $cta_link['url'] ); ?>"
				<?php if ( ! empty( $cta_link['target'] ) ) : ?>target="<?php echo esc_attr( $cta_link['target'] ); ?>" rel="noopener"<?php endif; ?>>
				<?php echo esc_html( $cta_link['title'] ?: __( 'Subscribe', 'dimuone-child' ) ); ?>
			</a>
			<?php endif; ?>

			<?php if ( ! empty( $cta['text'] ) ) : ?>
			<div class="footer__subscribe-note"><?php echo wp_kses_post( $cta['text'] ); ?></div>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>

</div>
