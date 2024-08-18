<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package DigitalResume
 */

get_header();
?>
	<main id="primary" class="main-wrapper d-flex align-items-center justify-content-center" style="height: 100vh;">

		<section class="error-404 not-found text-center">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'digitalresume' ); ?></h1>
			</header><!-- .page-header -->
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
