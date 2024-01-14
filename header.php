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
	<title>David Hicka &amp; Software Engineer Portfolio</title>
	
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="DevCard Bootstrap 5 Template">
	<meta name="author" content="Xiaoying Riley at 3rd Wave Media">    
	<link rel="shortcut icon" href="favicon.ico"> 
	
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
	
	<!-- FontAwesome JS -->
    <script defer src="assets/fontawesome/js/all.js"></script>
    
    <!-- Plugin CSS -->
    <link rel="stylesheet" href="assets/plugins/tiny-slider/tiny-slider.css">

	<!-- Theme CSS -->  
	<link id="theme-style" rel="stylesheet" href="assets/css/theme-1.css">

</head> 

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary">
			<?php esc_html_e('Skip to content', 'digitalresume'); ?>
		</a>

		<header id="masthead" class="site-header">

			<nav id="site-navigation" class="main-navigation" role="navigation">
				<div class="nav-wrapper container">
					<a href="#!" class="brand-logo">David Hicka</a>
					<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
					<ul class="right hide-on-med-and-down">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id' => 'primary-menu',
							)
						);
						?>
					</ul>
				</div>
			</nav>
			<ul class="sidenav" id="mobile-demo">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-1',
						'menu_id' => 'primary-menu',
					)
				);
				?>
			</ul>
		</header><!-- #masthead -->