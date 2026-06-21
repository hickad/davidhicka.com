<?php
/**
 * The header for the modern editorial DigitalResume theme.
 * Replaces the old fixed green sidebar with a sticky editorial header.
 *
 * @package DigitalResume
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="dh-header">
	<a class="dh-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<span class="dh-brand-mark">
			<?php
			// Uses the WP custom logo if set, else a "DH" monogram.
			if ( has_custom_logo() ) {
				$logo_id = get_theme_mod( 'custom_logo' );
				echo wp_get_attachment_image( $logo_id, 'thumbnail' );
			} else {
				echo 'DH';
			}
			?>
		</span>
		<span class="dh-brand-name"><?php bloginfo( 'name' ); ?></span>
		<span class="dh-brand-aud"><?php echo esc_html( digitalresume_audience_label() ); ?></span>
	</a>

	<nav class="dh-nav">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'menu-1',
			'container'      => false,
			'items_wrap'     => '%3$s',
			'fallback_cb'    => false,
			'depth'          => 1,
		) );
		?>
	</nav>
</header>
