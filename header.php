<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package DigitalResume
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php wp_title( '|', true, 'right' ); bloginfo( 'name' ); ?></title>
	<meta name="description" content="<?php bloginfo( 'description' ); ?>">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<header class="header text-center">
			<div class="force-overflow">
			<h1 class="blog-name pt-lg-4 mb-0"><a class="no-text-decoration" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo( 'name' ); ?></a></h1>

				<nav class="navbar navbar-expand-lg navbar-dark">
					<button class="navbar-toggler hamburger hamburger--squeeze" type="button" 
							data-bs-toggle="collapse" data-bs-target="#navigation" 
							aria-controls="navigation" aria-expanded="false" 
							aria-label="<?php esc_attr_e( 'Toggle navigation', 'DigitalResume' ); ?>">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</button>

					<div id="navigation" class="collapse navbar-collapse flex-column">
						<div class="profile-section pt-3 pt-lg-0">
							<?php
							$upload_dir = wp_get_upload_dir();
							$image_url = $upload_dir['baseurl'] . '/2024/07/profilePic.jpg';
							?>
							<img class="profile-image mb-3 rounded-circle mx-auto" src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Profile Image', 'DigitalResume' ); ?>">
							<div class="bio mb-3"><?php _e( "Hi, my name is David Hicka and I'm a software engineer. Welcome to my personal website!", 'DigitalResume' ); ?></div>
							<a href="https://www.linkedin.com/in/davidhicka/" target="_blank" class="linkedin-icon" aria-label="<?php esc_attr_e( "Visit David Hicka's LinkedIn profile", 'DigitalResume' ); ?>" rel="noopener noreferrer">
								<i class="fab fa-linkedin-in fa-fw fa-lg" aria-hidden="true"></i>
							</a>
							<hr>
						</div><!--//profile-section-->

						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'container'      => false,
								'items_wrap'     => '<ul class="navbar-nav flex-column text-center">%3$s</ul>',
								'walker'         => new Custom_Nav_Walker(),
							)
						);
						?>

						<div class="dark-mode-toggle text-center w-100">
							<hr class="mb-4">
							<h4 class="toggle-name mb-3 "><i class="fas fa-adjust me-1"></i><?php _e( 'Light Mode', 'DigitalResume' ); ?></h4>

							<input class="toggle" id="darkmode" type="checkbox">
							<label class="toggle-btn mx-auto mb-0" for="darkmode">
								<span class="visually-hidden"><?php _e( 'Toggle Dark Mode', 'DigitalResume' ); ?></span>
							</label>
						</div><!--//dark-mode-toggle-->

					</div><!--//navbar-collapse-->
				</nav>
			</div><!--//force-overflow-->
		</header><!--//header-->

		<!-- Additional content goes here -->
