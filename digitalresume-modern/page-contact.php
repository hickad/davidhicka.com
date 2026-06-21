<?php
/**
 * page-contact.php — contact page (editorial CTA + the page's own content/form).
 * Drop your form-plugin shortcode into the page body in wp-admin; it will be
 * styled by the design-system form rules. A mailto CTA is shown alongside.
 *
 * @package DigitalResume
 */
get_header();
?>

<main>
	<section class="dh-hero dh-shell" style="padding-block:4.5rem 2rem;">
		<span class="dh-kicker">Let's talk</span>
		<h1 class="dh-hero-name" style="font-size:clamp(2rem,4vw,3rem);max-width:18ch;">Available for senior engineering roles.</h1>
		<div class="dh-actions">
			<a class="btn btn-primary" href="mailto:hickad@gmail.com"><i class="fas fa-envelope me-2"></i>hickad@gmail.com</a>
			<a class="btn btn-outline-primary" href="https://www.linkedin.com/in/davidhicka/"><i class="fab fa-linkedin-in me-2"></i>LinkedIn</a>
		</div>
	</section>

	<?php
	while ( have_posts() ) :
		the_post();
		if ( trim( get_the_content() ) ) :
			?>
			<section class="dh-section dh-shell" style="border-top:none;padding-top:0;">
				<div style="max-width:600px;"><?php the_content(); ?></div>
			</section>
			<?php
		endif;
	endwhile;
	?>
</main>

<?php get_footer(); ?>
