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
<?php wp_head(); 

	// Get the upload directory information
	$upload_dir = wp_get_upload_dir();

	// Construct the full URL of the image
	$image_url = $upload_dir['baseurl'] . '/2024/05/profilePic.jpg';

?>
<head>
	<title>David Hicka - Software Engineer</title>
	
	<!-- Meta -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="David Hicka - Software Engineer">
	<!-- <link rel="shortcut icon" href="favicon.ico">  -->
	
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
	
</head> 

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary">
			<?php esc_html_e('Skip to content', 'digitalresume'); ?>
		</a>
	
		<header class="header text-center">	    
		<div class="force-overflow">
			<h1 class="blog-name pt-lg-4 mb-0"><a class="no-text-decoration" href="index.html">David Hicka</a></h1>
			
			<nav class="navbar navbar-expand-lg navbar-dark" >
				
			<button class="navbar-toggler hamburger hamburger--squeeze" type="button" 
					data-bs-toggle="collapse" data-bs-target="#navigation" 
					aria-controls="navigation" aria-expanded="false" 
					aria-label="Toggle navigation">
				<span class="hamburger-box">
					<span class="hamburger-inner"></span>
				</span>
			</button>

				<div id="navigation" class="collapse navbar-collapse flex-column" >
					<div class="profile-section pt-3 pt-lg-0">	
						<img class="profile-image mb-3 rounded-circle mx-auto" src="<?php echo esc_url( $image_url ); ?>" alt="Profile Image">					
						<div class="bio mb-3">Hi, my name is David Hicka and I'm a software engineer. Welcome to my personal website!</div><!--//bio-->
						<ul class="social-list list-inline py-2 mx-auto">
						<li class="list-inline-item mb-3">
							<a href="https://www.linkedin.com/in/davidhicka/" target="_blank">
								<i class="fab fa-linkedin-in fa-fw fa-lg"></i>
							</a>
							</li>
						</ul><!--//social-list-->


						<hr> 
					</div><!--//profile-section-->
															
					<ul class="navbar-nav flex-column text-start">
					<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'walker'         => new Custom_Nav_Walker()
							)
						);
						?>
					</ul>
					
					<div class="dark-mode-toggle text-center w-100">
						<hr class="mb-4">
					    <h4 class="toggle-name mb-3 "><i class="fas fa-adjust me-1"></i>Light Mode</h4>
					    
					    <input class="toggle" id="darkmode" type="checkbox">
					    <label class="toggle-btn mx-auto mb-0" for="darkmode"></label>
					    
					</div><!--//dark-mode-toggle-->
					
				</div>
				
			</nav>
		</div><!--//force-overflow-->
		</header>


		