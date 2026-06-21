<?php
/**
 * page-projects.php — projects gallery using the Portfolio CPT.
 * Falls back to a curated static set if no portfolio posts exist yet.
 * Honours WordPress page-password protection (the theme's custom form).
 *
 * @package DigitalResume
 */
get_header();

// Respect WP password protection on this page.
if ( post_password_required() ) {
	echo '<section class="dh-section dh-shell">' . get_the_password_form() . '</section>';
	get_footer();
	return;
}
?>

<main>
	<section class="dh-hero dh-shell" style="padding-block:4rem 2.5rem;">
		<span class="dh-kicker">Selected Work</span>
		<h1 class="dh-hero-name" style="font-size:clamp(2.5rem,5vw,4rem);">Projects</h1>
	</section>

	<section class="dh-section dh-shell" style="border-top:none;padding-top:0;">
		<div class="dh-cards">
			<?php
			$portfolio = new WP_Query( array(
				'post_type'      => 'portfolio',
				'posts_per_page' => 12,
			) );

			if ( $portfolio->have_posts() ) :
				while ( $portfolio->have_posts() ) : $portfolio->the_post();
					?>
					<article class="dh-card">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="dh-card-img"><?php the_post_thumbnail( 'large' ); ?></div>
						<?php endif; ?>
						<div class="dh-card-body">
							<h3 class="dh-card-title"><?php the_title(); ?></h3>
							<div class="dh-card-kind"><?php echo esc_html( get_the_date() ); ?></div>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				// Curated fallback (edit freely, or create Portfolio posts in wp-admin).
				$fallback = array(
					array( 'Deal Pack Web', 'Lead Engineer · 2017–Present', 'SOC 2 / PCI financial management & loan-servicing platform for dealerships and finance companies.' ),
					array( 'Defense Training Systems', 'L3 · 2004–2014', 'Interactive Level-3 courseware and XML-driven cockpit simulators for AFSOC, USMC & USN programs.' ),
					array( 'Florida Blue · Member Tools', 'UI Developer · 2017', 'WCAG 2.0 AA upgrades and a HealthCare.gov enrollment integration.' ),
					array( 'Toon & Tails', 'Founder & Engineer · 2025', 'Production AI SaaS turning pet photos into cartoon portraits and printed merch. Built solo.' ),
				);
				foreach ( $fallback as $p ) :
					?>
					<article class="dh-card">
						<div class="dh-card-body">
							<h3 class="dh-card-title"><?php echo esc_html( $p[0] ); ?></h3>
							<div class="dh-card-kind"><?php echo esc_html( $p[1] ); ?></div>
							<p><?php echo esc_html( $p[2] ); ?></p>
						</div>
					</article>
					<?php
				endforeach;
			endif;
			?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
