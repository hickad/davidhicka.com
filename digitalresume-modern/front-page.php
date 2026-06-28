<?php
/**
 * front-page.php — single-page home.
 * Hero + Work + Experience + Skills + Contact, linked from the header nav.
 *
 * @package DigitalResume
 */
get_header();

// Résumé content (single source of truth, edited under Dashboard → Résumé).
// Drives the hero download buttons plus the Experience and Skills sections.
$dh_aud        = function_exists( 'digitalresume_audience' ) ? digitalresume_audience() : 'finance';
$dh_resume     = function_exists( 'dhm_resume_get' ) ? dhm_resume_get( $dh_aud ) : null;
$dh_has_resume = $dh_resume && function_exists( 'dhm_resume_has_content' ) && dhm_resume_has_content( $dh_resume );
?>

<main>
	<!-- HERO -->
	<section class="dh-hero dh-shell">
		<div class="dh-hero-grid">
			<div class="dh-hero-text">
				<?php $dh_viewer = function_exists( 'dhm_viewer_label' ) ? dhm_viewer_label() : ''; ?>
				<?php if ( $dh_viewer ) : ?>
					<p class="dh-welcome">Welcome, <strong><?php echo esc_html( $dh_viewer ); ?></strong></p>
				<?php endif; ?>
				<span class="dh-kicker">Senior Software Engineer</span>
				<h1 class="dh-hero-name"><?php bloginfo( 'name' ); ?></h1>
				<p class="dh-hero-lead"><?php echo esc_html( digitalresume_audience_content( 'lead' ) ); ?></p>

				<div class="dh-actions">
					<?php if ( $dh_has_resume ) : ?>
						<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/resume/' . $dh_aud . '.pdf' ) ); ?>">
							<i class="fas fa-file-pdf me-2"></i>Download PDF
						</a>
						<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/resume/' . $dh_aud . '.docx' ) ); ?>">
							<i class="fas fa-file-word me-2"></i>Download Word
						</a>
					<?php endif; ?>
					<a class="btn btn-ghost" href="#contact">
						Get in touch <i class="fas fa-arrow-right ms-2"></i>
					</a>
				</div>

				<div class="dh-stats">
					<div>
						<div class="dh-stat-num">20<span>+</span></div>
						<div class="dh-stat-label">Years shipping software</div>
					</div>
					<div>
						<div class="dh-stat-num">2,000<span>+</span></div>
						<div class="dh-stat-label">Customers on a product I built solo</div>
					</div>
					<div>
						<div class="dh-stat-num">SOC&nbsp;2<span>·</span>PCI</div>
						<div class="dh-stat-label">Audited environments</div>
					</div>
				</div>
			</div><!-- /.dh-hero-text -->

			<div class="dh-hero-media">
				<?php
				$hero_photo = wp_get_attachment_image(
					119,
					'full',
					false,
					array(
						'class' => 'dh-hero-photo',
						'alt'   => get_bloginfo( 'name' ),
					)
				);
				echo $hero_photo ? $hero_photo : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div><!-- /.dh-hero-grid -->
	</section>

	<!-- WORK -->
	<section class="dh-section dh-shell" id="work">
		<div class="dh-section-head">
			<span class="dh-section-num">01</span>
			<h2 class="dh-section-title">Work</h2>
		</div>
		<div class="dh-cards">
			<?php
			$portfolio = new WP_Query(
				array(
					'post_type'      => 'portfolio',
					'posts_per_page' => 12,
				)
			);

			if ( $portfolio->have_posts() ) :
				while ( $portfolio->have_posts() ) :
					$portfolio->the_post();
					?>
					<?php
					$dh_card_img = has_post_thumbnail()
						? get_the_post_thumbnail_url( get_the_ID(), 'large' )
						: get_post_meta( get_the_ID(), '_dhm_proj_image', true );
					?>
					<article class="dh-card">
						<?php if ( $dh_card_img ) : ?>
							<div class="dh-card-img"><img src="<?php echo esc_url( $dh_card_img ); ?>" alt="<?php the_title_attribute(); ?>"></div>
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
				$fallback = array(
					array( 'Deal Pack Web', 'Lead Engineer · 2019–Present', 'SOC 2 / PCI financial management & loan-servicing platform for dealerships and finance companies.' ),
					array( 'Defense Training Systems', 'L3 · 2004–2014', 'Interactive Level-3 courseware and XML-driven cockpit simulators for AFSOC, USMC & USN programs.' ),
					array( 'Florida Blue · Member Tools', 'UI Developer · 2015–2017', 'WCAG 2.0 AA upgrades and a HealthCare.gov enrollment integration.' ),
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
		<div style="margin-top:2.5rem;">
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/projects/' ) ); ?>">
				View all projects <i class="fas fa-arrow-right ms-2"></i>
			</a>
		</div>
	</section>

	<!-- EXPERIENCE -->
	<section class="dh-section dh-shell" id="experience">
		<div class="dh-section-head">
			<span class="dh-section-num">02</span>
			<h2 class="dh-section-title">Experience</h2>
		</div>
		<?php
		// Driven by the résumé data (Dashboard → Résumé).
		echo $dh_has_resume ? dhm_resume_experience_html( $dh_resume ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</section>

	<!-- SKILLS -->
	<section class="dh-section dh-shell" id="skills">
		<div class="dh-section-head">
			<span class="dh-section-num">03</span>
			<h2 class="dh-section-title">Skills</h2>
		</div>
		<?php
		// Skills chips and the education line, driven by the résumé data.
		echo $dh_has_resume ? dhm_resume_skills_html( $dh_resume ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $dh_has_resume ? dhm_resume_education_html( $dh_resume ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</section>

	<!-- CONTACT -->
	<section class="dh-section dh-shell" id="contact">
		<div class="dh-section-head">
			<span class="dh-section-num">04</span>
			<h2 class="dh-section-title">Contact</h2>
		</div>
		<p class="dh-hero-lead" style="max-width:48ch;font-size:1.15rem;">
			<?php if ( $dh_viewer ) : ?>Thanks for taking a look, <strong><?php echo esc_html( $dh_viewer ); ?></strong>. <?php endif; ?>Available for senior engineering roles. Send a message below, or reach me on
			<a href="https://www.linkedin.com/in/davidhicka/">LinkedIn</a>.
		</p>
		<div class="dh-contact-form">
			<?php echo do_shortcode( '[contact-form-7 id="734188d" title="Contact Page Form"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
